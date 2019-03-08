<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 17/01/19
 * Time: 10:13
 */

namespace Modules\Generator\Console\API;

use InfyOm\Generator\Commands\API\TestsGeneratorCommand as InfyOmTestsGeneratorCommand;
use Modules\Generator\Common\CommandData;
use Modules\Generator\Common\Traits\BaseCommandTrait;

class TestsGeneratorCommand extends InfyOmTestsGeneratorCommand
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
    protected $name = 'generate.api:tests';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_API);
    }
}