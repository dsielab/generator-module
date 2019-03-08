<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 17/01/19
 * Time: 20:27
 */

namespace Modules\Generator\Common;

use Illuminate\Console\Command;
use InfyOm\Generator\Common\CommandData as InfyOmCommandData;

class CommandData extends InfyOmCommandData
{
    public static $COMMAND_TYPE_PUBLISH = 'publish';

    /**
     * @param Command $commandObj
     * @param string  $commandType
     */
    public function __construct(Command $commandObj, string $commandType)
    {
        parent::__construct($commandObj, $commandType);

        // Overriding config object
        $this->config = new GeneratorConfig();
    }
}