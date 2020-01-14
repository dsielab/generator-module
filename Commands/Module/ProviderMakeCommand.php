<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 22:06
 */

namespace Modules\Generator\Console\Module;

use Nwidart\Modules\Commands\ProviderMakeCommand as NwidartProviderMakeCommand;
use Nwidart\Modules\Module;
use Nwidart\Modules\Support\Config\GenerateConfigReader;

class ProviderMakeCommand extends NwidartProviderMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:module-provider';

    /**
     * Get content from stub template
     *
     * @return mixed
     */
    protected function getTemplateContents()
    {
        /** @var Module $module */
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());
        $LOWER_NAME = $module->getLowerName();
        $PATH_CONFIG = GenerateConfigReader::read('config')->getPath();

        // Change config file name to modules.{$LOWER_NAME}. This generate location path modules/{$LOWER_NAME}
        return str_replace(
            [
                '__DIR__.\'/../'.$PATH_CONFIG.'/config.php\' => config_path(\''.$LOWER_NAME.'.php\')',
                '__DIR__.\'/../'.$PATH_CONFIG.'/config.php\', \''.$LOWER_NAME.'\''
            ],
            [
                '__DIR__.\'/../'.$PATH_CONFIG.'/config.php\' => config_path(\'modules/'.$LOWER_NAME.'.php\')',
                '__DIR__.\'/../'.$PATH_CONFIG.'/config.php\', \'modules.'.$LOWER_NAME.'\''
            ],
            parent::getTemplateContents()
        );
    }
}