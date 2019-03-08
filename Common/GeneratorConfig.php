<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 16/01/19
 * Time: 17:18
 */

namespace Modules\Generator\Common;

use Illuminate\Support\Str;
use InfyOm\Generator\Common\CommandData;
use InfyOm\Generator\Common\GeneratorConfig as InfyOmGeneratorConfig;

class GeneratorConfig extends InfyOmGeneratorConfig
{
    /* Namespace variables */
    public $nsModule;

    /* Path variables */
    public $pathModule;

    /* Command Options */
    public static $availableOptions = [
        'module',
        'fieldsFile',
        'jsonFromGUI',
        'tableName',
        'fromTable',
        'save',
        'primary',
        'prefix',
        'paginate',
        'skip',
        'datatables',
        'views',
        'relations',
    ];

    /**
     * @param CommandData $commandData
     * @param null $options
     */
    public function init(CommandData &$commandData, $options = null)
    {
        // Getting module name option or active if exists
        $moduleOption = Str::studly($commandData->commandObj->option('module')) ?: app('modules')->getUsedNow();

        // If it is set and module exists do the stuff else show an error message
        if (!empty($moduleOption)) {
            if (app('modules')->has($moduleOption)) {
                $this->changeConfig($moduleOption);
            } else {
                $commandData->commandObj->error("Module [{$moduleOption}] does not exists.");
            }
        }

        parent::init($commandData, $options);
    }

    /**
     * @param CommandData $commandData
     */
    public function loadNamespaces(CommandData &$commandData)
    {
        // Get if module option is passed
        $module = $commandData->commandObj->option('module');

        if ($module) {
            $this->nsModule = 'Modules\\'.$module;
        }

        parent::loadNamespaces($commandData);
    }

    /**
     * @param CommandData $commandData
     * @return CommandData
     */
    public function loadDynamicVariables(CommandData &$commandData)
    {
        if ($this->nsModule) {
            $commandData->addDynamicVariable('$NAMESPACE_MODULE', $this->nsModule);
        }

        return parent::loadDynamicVariables($commandData);
    }

    /**
     * Change the paths and namespaces definitions for generator to the specific target module
     *
     * @param $moduleName
     */
    protected function changeConfig($moduleName)
    {
        // Getting paths patterns
        $modulePaths = app('config')->get('modules.generator.path');

        // Getting namespaces patterns
        $moduleNamespaces = app('config')->get('modules.generator.namespace');

        // Replacing paths
        array_walk($modulePaths, function (&$item, $key, $module) {
            $item = str_replace('{Module}', $module, $item);
        }, $moduleName);

        // Replacing namespaces
        array_walk($moduleNamespaces, function (&$item, $key, $module) {
            $item = str_replace('{Module}', $module, $item);
        }, $moduleName);

        // Overriding paths
        app('config')->set('infyom.laravel_generator.path', $modulePaths);

        // Overriding namespaces
        app('config')->set('infyom.laravel_generator.namespace', $moduleNamespaces);
    }
}