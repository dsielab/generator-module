<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 14:40
 */

namespace Modules\Generator\Console\Module;

use Nwidart\Modules\Commands\UseCommand as NwidartUseCommand;

class UseCommand extends NwidartUseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:module-use';
}