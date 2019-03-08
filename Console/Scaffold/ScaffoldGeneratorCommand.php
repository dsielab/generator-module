<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 16/01/19
 * Time: 18:18
 */

namespace Modules\Generator\Console\Scaffold;

use InfyOm\Generator\Commands\Scaffold\ScaffoldGeneratorCommand as InfyOmScaffoldGeneratorCommand;
use Modules\Generator\Common\CommandData;
use Modules\Generator\Common\Traits\BaseCommandTrait;

class ScaffoldGeneratorCommand extends InfyOmScaffoldGeneratorCommand
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
    protected $name = 'generate:scaffold';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD);
    }
}