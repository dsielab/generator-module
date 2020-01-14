<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 17/01/19
 * Time: 10:23
 */

namespace Modules\Generator\Console\VueJs;

use InfyOm\Generator\Commands\VueJs\VueJsGeneratorCommand as InfyOmVueJsGeneratorCommand;
use Modules\Generator\Common\CommandData;
use Modules\Generator\Common\Traits\BaseCommandTrait;

class VueJsGeneratorCommand extends InfyOmVueJsGeneratorCommand
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
    protected $name = 'generate:vuejs';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_VUEJS);
    }
}