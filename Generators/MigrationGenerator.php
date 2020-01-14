<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 20:44
 */

namespace Modules\Generator\Generators;

use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InfyOm\Generator\Generators\MigrationGenerator as InfyOmMigrationGenerator;
use InfyOm\Generator\Utils\FileUtil;
use Modules\Generator\Common\CommandData;
use SplFileInfo;

class MigrationGenerator extends InfyOmMigrationGenerator
{
    /** @var CommandData */
    protected $commandData;

    /** @var string */
    protected $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        // Getting module name option or active if exists
        $moduleOption = Str::studly($commandData->commandObj->option('module')) ?: app('modules')->getUsedNow();
        // Replace in path string
        $this->path = str_replace(
            '{Module}',
            $moduleOption,
            config('modules.generator.path.migration', base_path('database/migrations/'))
        );
    }

    public function generate()
    {
        $templateData = get_template('migration', 'laravel-generator');

        // Adding missing use statement
        $needles = 'use Illuminate\Support\Facades\Schema;';
        $replace = '<?php'.infy_nl(2);
        if (!Str::contains($templateData, $needles)) {
            $templateData = str_replace($replace, $replace.$needles.infy_nl(), $templateData);
        }

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        $templateData = str_replace('$FIELDS$', $this->generateFields(), $templateData);

        $tableName = $this->commandData->dynamicVars['$TABLE_NAME$'];

        $fileName = date('Y_m_d_His').'_'.'create_'.$tableName.'_table.php';

        FileUtil::createFile($this->path, $fileName, $templateData);

        $this->commandData->commandComment("\nMigration created: ");
        $this->commandData->commandInfo($fileName);
    }

    protected function generateFields()
    {
        $fields = [];
        $foreignKeys = [];
        $createdAtField = null;
        $updatedAtField = null;

        foreach ($this->commandData->fields as $field) {
            if ($field->name == 'created_at') {
                $createdAtField = $field;
                continue;
            } else {
                if ($field->name == 'updated_at') {
                    $updatedAtField = $field;
                    continue;
                }
            }

            $fields[] = $field->migrationText;
            if (!empty($field->foreignKeyText)) {
                $foreignKeys[] = $field->foreignKeyText;
            }
        }

        if ($createdAtField and $updatedAtField) {
            $fields[] = '$table->timestamps();';
        } else {
            if ($createdAtField) {
                $fields[] = $createdAtField->migrationText;
            }
            if ($updatedAtField) {
                $fields[] = $updatedAtField->migrationText;
            }
        }

        if ($this->commandData->getOption('softDelete')) {
            $fields[] = '$table->softDeletes();';
        }

        return implode(infy_nl_tab(1, 3), array_merge($fields, $foreignKeys));
    }

    /**
     * Overriding rollback method to avoid commandData private property error and rollback migration
     */
    public function rollback()
    {
        $fileName = 'create_'.$this->commandData->config->tableName.'_table.php';

        /** @var SplFileInfo $allFiles */
        $allFiles = File::allFiles($this->path);

        $files = [];

        foreach ($allFiles as $file) {
            /** @var SplFileInfo $file */
            $files[] = $file->getFilename();
        }

        $files = array_reverse($files);

        foreach ($files as $file) {
            if (Str::contains($file, $fileName)) {
                // Check if a given migration is already applied and ask for rollback
                $migrationName = Str::replaceFirst('.php', '', $file);
                $batch = $this->getBatchNumber($migrationName);
                if ($batch) {
                    $migrations = $this->getMigrationsFromBatchNumber($batch);
                    $migrationsCount = $migrations->count();

                    if ($migrationsCount && $this->commandData->commandObj->confirm("\nDo you want to rollback database migration ($migrationName)? [y|N]", false)) {

                        $canRollback = true;
                        if ($migrations->count() > 1) {
                            $this->commandData->commandWarn("\nTarget migration ($migrationName) is related with other migrations: \n".$migrations->implode("\n"));
                            $canRollback = $this->commandData->commandObj->confirm("\nAre you sure to rollback all migrations in the list? [y|N]", false);
                        }

                        if ($canRollback) {
                            $this->commandData->commandObj->call('migrate:rollback', array_filter([
                                '--path' => $this->path,
                                '--step' => $batch,
                                '--force' => true,
                            ]));
                        }
                    }
                }
                if ($this->rollbackFile($this->path, $file)) {
                    $this->commandData->commandComment('Migration file deleted: '.$file);
                }
                break;
            }
        }
    }

    /**
     * Check if a given migration is already applied
     *
     * @param string $migrationName
     * @return bool
     */
    protected function checkAppliedMigration(string $migrationName)
    {
        return DB::table('migrations')
            ->where('migration', $migrationName)
            ->exists();
    }

    /**
     * Return batch number given migration name
     *
     * @param string $migrationName
     * @return int
     */
    protected function getBatchNumber(string $migrationName)
    {
        if ($this->checkAppliedMigration($migrationName)) {
            return (int)DB::table('migrations')
                ->where('migration', $migrationName)
                ->value('batch');
        }

        return 0;
    }


    /**
     * Return all collections under specified batch number
     *
     * @param int $batch
     * @param bool $asString return collection as string
     * @param string $separator character separator if $asString is true
     * @return \Illuminate\Support\Collection | \Illuminate\Support\Collection|string
     */
    protected function getMigrationsFromBatchNumber(int $batch, $asString = false, $separator = "\n")
    {
        $migrations = DB::table('migrations')
            ->where('batch', $batch)
            ->select('migration')
            ->get();

        return !$asString ? $migrations : $migrations->implode($separator);
    }
}
