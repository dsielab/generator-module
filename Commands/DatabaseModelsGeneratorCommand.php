<?php


namespace Modules\Generator\Console;


use Illuminate\Support\Str;
use InfyOm\Generator\Commands\BaseCommand;
use InfyOm\Generator\Utils\FileUtil;
use Modules\Generator\Common\CommandData;
use Modules\Generator\Common\Traits\BaseCommandTrait;
use Modules\Generator\Common\Traits\DatabaseModelGeneratorHelperTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DatabaseModelsGeneratorCommand extends BaseCommand
{
    use BaseCommandTrait;
    use DatabaseModelGeneratorHelperTrait;

    /** @var string */
    protected $name = 'generate:models_file';

    /**
     * DatabaseModelGeneratorCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_MODELS_FILE);
    }

    /** @var string  */
    protected $description = 'Perform a database scan and mapping to generate a json file containing models definitions for generate:bulk_scaffold command.';

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $path = $this->commandData->commandObj->argument('path');

        if (!is_dir($path)) {
            $this->error('Invalid provided param (path).');
            exit;
        }

        if (!is_writeable($path)) {
            $this->error('Yoy have not permission to write in provided path. Please change permissions and try again.');
            exit;
        }

        $usedModule = Str::studly($this->commandData->commandObj->option('module')) ?: app('modules')->getUsedNow();

        if (!$usedModule) {
            $this->error('No target module defined. Please provide --module param. See (generate:models_file --help) for help.');
            exit;
        }

        $content = $this->generate($usedModule);

        if (empty($content)) {
            $this->info('No models found to generate the file.');
            $this->info('Exiting...');
            exit;
        }

        FileUtil::createFile($path.'/', 'models.json', $content);

        $this->info('Definitions file was generated successfully on: '.$path.'/models.json');
        $this->alert('Please review the file to correct the data to be finally used on scaffold generation!');
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            ['module', null, InputOption::VALUE_REQUIRED, 'Specify if you want to generate scaffold for specific module.']
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['path', InputArgument::REQUIRED, 'Path where the generated file will be stored.']
        ];
    }
}