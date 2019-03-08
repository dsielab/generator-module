<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 17/01/19
 * Time: 10:42
 */

namespace Modules\Generator\Common\Traits;

use InfyOm\Generator\Generators\Scaffold\RoutesGenerator;
use Modules\Generator\Generators\MigrationGenerator;
use Modules\Generator\Generators\ModelGenerator;
use Modules\Generator\Generators\RepositoryGenerator;
use Modules\Generator\Generators\Scaffold\ControllerGenerator;
use Modules\Generator\Generators\Scaffold\MenuGenerator;
use Modules\Generator\Generators\Scaffold\RequestGenerator;
use Modules\Generator\Generators\Scaffold\ViewGenerator;
use Symfony\Component\Console\Input\InputOption;

trait BaseCommandTrait
{
    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(
            [['module', null, InputOption::VALUE_REQUIRED, 'Specify if you want to generate scaffold for specific module.']],
            parent::getOptions()
        );
    }

    /**
     * Overriding BaseCommand scaffold generator function
     */
    public function generateCommonItems()
    {
        if (!$this->commandData->getOption('fromTable') and !$this->isSkip('migration')) {
            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
        }

        if (!$this->isSkip('model')) {
            $modelGenerator = new ModelGenerator($this->commandData);
            $modelGenerator->generate();
        }

        if (!$this->isSkip('repository')) {
            $repositoryGenerator = new RepositoryGenerator($this->commandData);
            $repositoryGenerator->generate();
        }
    }

    /**
     * Overriding BaseCommand scaffold generator function
     */
    public function generateScaffoldItems()
    {
        if (!$this->isSkip('requests') and !$this->isSkip('scaffold_requests')) {
            $requestGenerator = new RequestGenerator($this->commandData);
            $requestGenerator->generate();
        }

        if (!$this->isSkip('controllers') and !$this->isSkip('scaffold_controller')) {
            $controllerGenerator = new ControllerGenerator($this->commandData);
            $controllerGenerator->generate();
        }

        if (!$this->isSkip('views')) {
            $viewGenerator = new ViewGenerator($this->commandData);
            $viewGenerator->generate();
        }

        if (!$this->isSkip('routes') and !$this->isSkip('scaffold_routes')) {
            $routeGenerator = new RoutesGenerator($this->commandData);
            $routeGenerator->generate();
        }

        if (!$this->isSkip('menu') and $this->commandData->config->getAddOn('menu.enabled')) {
            $menuGenerator = new MenuGenerator($this->commandData);
            $menuGenerator->generate();
        }
    }

    public function performPostActions($runMigration = false)
    {
        if ($this->commandData->getOption('save')) {
            $this->saveSchemaFile();
        }

        if ($runMigration) {
            if ($this->commandData->config->forceMigrate) {
                $this->call('migrate');
            } elseif (!$this->commandData->getOption('fromTable') and !$this->isSkip('migration')) {
                if ($this->commandData->getOption('jsonFromGUI')) {
                    $this->call('migrate');
                } elseif ($this->confirm("\nDo you want to migrate database? [y|N]", false)) {
                    $this->call('migrate');
                }
            }
        }
        if (!$this->isSkip('dump-autoload')) {
            $this->info('Generating autoload files');
            $this->composer->dumpOptimized();
        }
    }
}