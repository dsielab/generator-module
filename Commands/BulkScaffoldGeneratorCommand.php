<?php


namespace Modules\Generator\Console;

use Illuminate\Support\Str;
use InfyOm\Generator\Commands\BaseCommand;
use InfyOm\Generator\Utils\FileUtil;
use Modules\Generator\Common\CommandData;
use Modules\Generator\Common\Traits\BaseCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BulkScaffoldGeneratorCommand extends BaseCommand
{
    use BaseCommandTrait;

    /** @var string  */
    protected $name = 'generate:bulk_scaffold';

    /** @var string  */
    protected $description = 'Generate complete bulk scaffold for models within a JSON file containing the models definitions.';

    /** @var array  */
    protected $allowedTypes = [
        'api_scaffold' => 'generate:api_scaffold',
        'api' => 'generate:api',
        'scaffold' => 'generate:scaffold',
        'scaffold_requests' => 'generate.scaffold:requests',
        'vuejs' => 'generate:vuejs'
    ];

    /**
     * BulkScaffoldGeneratorCommand constructor.
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_API_SCAFFOLD_BULK);
    }

    /**
     * Handle command
     */
    public function handle()
    {
        // Getting json file content
        $filePath = $this->getFilePath();

        // Execute generation
        $this->doHandle($filePath);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['module', null, InputOption::VALUE_REQUIRED, 'Specify if you want to generate scaffold for specific module.'],
            ['type', null, InputOption::VALUE_REQUIRED, 'Define which generator will be executed (api_scaffold (from generate:api_scaffold) [default] | api (from generate:api) | scaffold (from generate:scaffold) | scaffold_requests (from generate.scaffold:requests) | vuejs (from generate:vuejs))'],
            ['save', null, InputOption::VALUE_NONE, 'Save model schema to file'],
            ['prefix', null, InputOption::VALUE_REQUIRED, 'Prefix for all files'],
            ['paginate', null, InputOption::VALUE_REQUIRED, 'Pagination for index.blade.php'],
            ['skip', null, InputOption::VALUE_REQUIRED, 'Skip Specific Items to Generate (migration,model,controllers,api_controller,scaffold_controller,repository,requests,api_requests,scaffold_requests,routes,api_routes,scaffold_routes,views,tests,menu,dump-autoload)'],
            ['datatables', null, InputOption::VALUE_REQUIRED, 'Override datatables settings'],
            ['views', null, InputOption::VALUE_REQUIRED, 'Specify only the views you want generated: index,create,edit,show'],
            ['relations', null, InputOption::VALUE_NONE, 'Specify if you want to pass relationships for fields'],
        ];
    }

    /**
     * Get definitions json content
     *
     * @return string
     */
    public function getFilePath()
    {
        $filePath = $this->commandData->commandObj->argument('bulkFieldsFile');

        if (!$filePath) {
            $this->commandData->commandError('Parameter [bulkFieldsFile] is required!');
            exit;
        }

        if (!file_exists($filePath)) {
            $this->commandData->commandError('Fields file not found');
            exit;
        }

        return $filePath;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['bulkFieldsFile', InputArgument::REQUIRED, 'JSON file containing full models definition.']
        ];
    }


    /**
     * Perform generation
     *
     * @param $filePath
     */
    public function doHandle($filePath)
    {
        $fileContents = file_get_contents($filePath);
        $jsonData = json_decode($fileContents, true);
        $params = [];
        $usedModule = Str::studly($this->commandData->commandObj->option('module')) ?: app('modules')->getUsedNow();

        if ($skip = $this->commandData->commandObj->option('skip')) {
            $params['--skip'] = $skip;
        }

        if (($type = $this->commandData->commandObj->option('type')) && !in_array($type, array_keys($this->allowedTypes))) {
            $this->error('Wrong type option see (generate:bulk_scaffold --help) for information about available values.');
            exit;
        } elseif(!$type) {
            $type = 'api_scaffold';
        }

        $moduleSchemaPath = str_replace('{Module}', $usedModule, app('config')->get('modules.generator.path.schema_files'));

        $this->info('Starting generation... Please wait, this may take a while!');

        foreach ($jsonData as $model => $fields) {

            FileUtil::createFile($moduleSchemaPath, $model . '.json', json_encode($fields));

            $this->call($this->allowedTypes[$type], array_merge([
                'model' => $model,
                '--module' => $usedModule,
                '--fieldsFile' => $model . '.json',
            ], $params));

            $this->info('Scaffold for model '.$model.' generated successfully!');
        }

        $this->info('Generating autoload files');
        $this->composer->dumpOptimized();
        $this->info('Bulk generation end successfully!');
    }
}