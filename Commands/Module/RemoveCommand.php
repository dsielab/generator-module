<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 13/02/19
 * Time: 10:21
 */

namespace Modules\Generator\Console\Module;


use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RemoveCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:module-remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the specified module.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = Str::studly($this->argument('module'));

        if (!$this->laravel['modules']->has($module)) {
            $this->error("Module [{$module}] does not exists.");

            return;
        }

        $this->info("Under construction, remove the module by your self ;)");
    }
}