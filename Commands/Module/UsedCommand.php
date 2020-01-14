<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 14:56
 */

namespace Modules\Generator\Console\Module;

use Nwidart\Modules\Commands\UnUseCommand as NwidartUnUseCommand;

class UsedCommand extends NwidartUnUseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:module-used';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the current used module with generate:module-use';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usedNow = $this->laravel['modules']->getUsedNow();

        if (!empty($usedNow)) {
            $this->info("Using module $usedNow.");
        } else {
            $this->info("No module is being used now.");
        }
    }
}