<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 11:47
 */

namespace Modules\Generator\Common\Traits;

use Modules\Generator\Common\CommandData;
use Symfony\Component\Console\Exception\InvalidArgumentException;

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
     * @throws \Exception
     */
    public function handle() {
        try {
            $module = $this->argument('module');
        } catch (InvalidArgumentException $e) {
            $module = app('modules')->getUsedNow();
        } catch (\Exception $e) {
            throw $e;
        }

        $this->commandData->config->changeConfig($module);

        parent::handle();
    }
}
