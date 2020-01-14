<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 14:56
 */

namespace Modules\Generator\Console\Module;

use Nwidart\Modules\Commands\UnUseCommand as NwidartUnUseCommand;

class UnUseCommand extends NwidartUnUseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:module-unuse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forget the used module with generate:use_module';
}