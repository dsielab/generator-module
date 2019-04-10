<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 20:57
 */

namespace Modules\Generator\Generators\API;

use Illuminate\Support\Str;
use Modules\Generator\Common\CommandData;
use InfyOm\Generator\Generators\API\APIControllerGenerator as InfyOmAPIControllerGenerator;
use InfyOm\Generator\Utils\FileUtil;

class APIControllerGenerator extends InfyOmAPIControllerGenerator
{
    /** @var CommandData  */
    protected $commandData;

    /** @var string */
    protected $path;

    /** @var string  */
    protected $fileName;

    /**
     * APIControllerGenerator constructor.
     * @param CommandData $commandData
     */
    public function __construct(CommandData $commandData)
    {
        parent::__construct($commandData);
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathApiController;
        $this->fileName = $this->commandData->modelName.'APIController.php';
    }

    /**
     * Overriding to ensure method call own fillDocs function
     */
    public function generate()
    {
        $templateData = get_template('api.controller.api_controller', 'laravel-generator');

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = $this->fillDocs($templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nAPI Controller created: ");
        $this->commandData->commandInfo($this->fileName);
    }

    /**
     * @param $templateData
     * @return mixed
     */
    protected function fillDocs($templateData)
    {
        $methods = ['controller', 'index', 'store', 'show', 'update', 'destroy'];

        if ($this->commandData->getAddOn('swagger')) {
            $templatePrefix = 'controller_docs';
            $templateType = 'swagger-generator';
        } else {
            $templatePrefix = 'api.docs.controller';
            $templateType = 'laravel-generator';
        }

        foreach ($methods as $method) {
            $key = '$DOC_'.strtoupper($method).'$';
            $docTemplate = get_template($templatePrefix.'.'.$method, $templateType);
            $docTemplate = fill_template($this->commandData->dynamicVars, $docTemplate);
            $templateData = str_replace($key, $docTemplate, $templateData);
        }

        // Overriding Controller Swagger Annotations
        if ($this->commandData->getAddOn('swagger')) {
            $module = $this->commandData->commandObj->option('module') ?: app('modules')->getUsedNow();
            $model  = $modelClass = $this->commandData->dynamicVars['$MODEL_NAME$'];
            $snake_name_module = Str::snake($module);
            $pluralLowerCamelModel = Str::camel(Str::plural($model));
            $snake_plural_name_model = Str::snake($pluralLowerCamelModel);

            $templateData = str_replace([
                'path="/'.$pluralLowerCamelModel,
                'tags={"',
            ], [
                'path="' . $snake_name_module . '/'.$snake_plural_name_model,
                'tags={"'. $module . '\\',
            ], $templateData);
        }

        return $templateData;
    }
}