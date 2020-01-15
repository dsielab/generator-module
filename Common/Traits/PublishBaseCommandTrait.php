<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 11:47
 */

namespace Modules\Generator\Common\Traits;

use Modules\Generator\Common\CommandData;

trait PublishBaseCommandTrait
{
    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_PUBLISH);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle() {
        $module = $this->argument('module') ?: app('modules')->getUsedNow();

        $module = app('modules')->findOrFail($module);

        $this->commandData->config->changeConfig($module);

        parent::handle();
    }
}
