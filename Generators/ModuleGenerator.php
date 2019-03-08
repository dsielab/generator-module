<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 22:20
 */

namespace Modules\Generator\Generators;

use Nwidart\Modules\Generators\ModuleGenerator as NwidartModuleGenerator;

class ModuleGenerator extends NwidartModuleGenerator
{
    /**
     * Generate some resources.
     */
    public function generateResources()
    {
        $this->console->call('module:make-seed', [
            'name' => $this->getName(),
            'module' => $this->getName(),
            '--master' => true,
        ]);

        $this->console->call('generate:module-provider', [
            'name' => $this->getName() . 'ServiceProvider',
            'module' => $this->getName(),
            '--master' => true,
        ]);

        $this->console->call('module:route-provider', [
            'module' => $this->getName(),
        ]);

        $this->console->call('module:make-controller', [
            'controller' => $this->getName() . 'Controller',
            'module' => $this->getName(),
        ]);
    }
}