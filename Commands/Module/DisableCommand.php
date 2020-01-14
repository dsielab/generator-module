<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 13/02/19
 * Time: 11:16
 */

namespace Modules\Generator\Console\Module;

use \Nwidart\Modules\Commands\DisableCommand as NwidartDisableCommand;

class DisableCommand extends NwidartDisableCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:module-disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable the specified module.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = $this->argument('module');
        $module = $this->laravel['modules']->findOrFail($moduleName);
        $usedNow = $this->laravel['modules']->getUsedNow();

        if ($module->enabled()) {
            $module->disable();

            if ($moduleName === $usedNow) {
                $this->call('generate:module-unuse');
            }

            $this->info("Module [{$module}] disabled successfully.");
        } else {
            $this->comment("Module [{$module}] has already disabled.");
        }
    }
}