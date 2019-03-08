<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 17/01/19
 * Time: 10:15
 */

namespace Modules\Generator\Console\Scaffold;

use InfyOm\Generator\Commands\Scaffold\ControllerGeneratorCommand as InfyOmControllerGeneratorCommand;
use Modules\Generator\Common\CommandData;
use Modules\Generator\Common\Traits\BaseCommandTrait;

class ControllerGeneratorCommand extends InfyOmControllerGeneratorCommand
{
    /**
     * Modifying Inheritance for InfyOm BaseCommand class to inject module param
     */
    use BaseCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate.scaffold:controller';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD);
    }
}