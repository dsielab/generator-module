<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 17/01/19
 * Time: 20:27
 */

namespace Modules\Generator\Common;

use Illuminate\Console\Command;
use InfyOm\Generator\Commands\BaseCommand;
use InfyOm\Generator\Common\CommandData as InfyOmCommandData;
use Modules\Generator\Common\Traits\BaseCommandTrait;

class CommandData extends InfyOmCommandData
{
    /** @var string  */
    public static $COMMAND_TYPE_PUBLISH = 'publish';

    /** @var string  */
    public static $COMMAND_TYPE_API_SCAFFOLD_BULK = 'api_scaffold_bulk';

    /** @var BaseCommandTrait | BaseCommand | Command */
    public $commandObj;

    /**
     * @param BaseCommandTrait | BaseCommand | Command $commandObj
     * @param string  $commandType
     */
    public function __construct(Command $commandObj, string $commandType)
    {
        parent::__construct($commandObj, $commandType);

        // Overriding config object
        $this->config = new GeneratorConfig();
    }
}