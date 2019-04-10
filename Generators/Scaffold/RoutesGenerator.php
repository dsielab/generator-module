<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 21:06
 */

namespace Modules\Generator\Generators\Scaffold;

use InfyOm\Generator\Common\CommandData;
use InfyOm\Generator\Generators\Scaffold\RoutesGenerator as InfyOmRoutesGenerator;

class RoutesGenerator extends InfyOmRoutesGenerator
{
    /** @var CommandData */
    protected $commandData;

    /** @var string */
    protected $path;

    /** @var string */
    protected $routeContents;

    /** @var string */
    protected $routesTemplate;

    /**
     * RoutesGenerator constructor.
     * @param CommandData $commandData
     */
    public function __construct(CommandData $commandData)
    {
        parent::__construct($commandData);
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathRoutes;
        $this->routeContents = file_get_contents($this->path);
        if (!empty($this->commandData->config->prefixes['route'])) {
            $this->routesTemplate = get_template('scaffold.routes.prefix_routes', 'laravel-generator');
        } else {
            $this->routesTemplate = get_template('scaffold.routes.routes', 'laravel-generator');
        }
        $this->routesTemplate = fill_template($this->commandData->dynamicVars, $this->routesTemplate);
    }

    /**
     *
     */
    public function generate()
    {
        $this->routeContents .= "\n\n".$this->routesTemplate;

        file_put_contents($this->path, $this->routeContents);
        $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' routes added.');
    }
}