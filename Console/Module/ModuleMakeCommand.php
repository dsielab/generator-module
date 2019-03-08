<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 12:17
 */

namespace Modules\Generator\Console\Module;

use Modules\Generator\Generators\ModuleGenerator;
use Nwidart\Modules\Commands\ModuleMakeCommand as NwidartModuleMakeCommand;

class ModuleMakeCommand extends NwidartModuleMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $names = $this->argument('name');

        if (!$names) {
            $this->error('No module name(s) provided!');
        }

        foreach ($names as $name) {
            with(new ModuleGenerator($name))
                ->setFilesystem($this->laravel['files'])
                ->setModule($this->laravel['modules'])
                ->setConfig($this->laravel['config'])
                ->setConsole($this)
                ->setForce($this->option('force'))
                ->setPlain($this->option('plain'))
                ->generate();
        }
    }
}