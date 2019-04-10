<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 21:09
 */

namespace Modules\Generator\Generators\VueJs;

use Illuminate\Support\Str;
use InfyOm\Generator\Generators\VueJs\ControllerGenerator as InfyOmVueJsControllerGenerator;
use InfyOm\Generator\Utils\FileUtil;
use Modules\Generator\Common\CommandData;

class ControllerGenerator extends InfyOmVueJsControllerGenerator
{
    /** @var CommandData */
    protected $commandData;

    /** @var string */
    protected $path;

    /** @var string */
    protected $fileName;

    /**
     * ControllerGenerator constructor.
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
     *
     */
    public function generate()
    {
        $templateData = get_template('vuejs.controller.api_controller', 'laravel-generator');

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = $this->fillDocs($templateData);

        $fields = $this->commandData->fields;
        $i = 0;
        $filter = '';
        $searchableCount = 0;
        foreach ($fields as $field) {
            if ($field['searchable']) {
                $searchableCount++;
            }
        }
        foreach ($fields as $field) {
            if ($field['searchable']) {
                if ($i == 0) {
                    $filter .= '$q->where("'.$field['fieldName'].'", "like", $value)';
                    if ($searchableCount == 1) {
                        $filter .= ';';
                    } else {
                        $filter .= "\n";
                    }
                } else {
                    if ($i == $searchableCount - 1) {
                        $filter .= '                  ->orWhere("'.$field['fieldName'].'", "like", $value);';
                    } else {
                        $filter .= '                  ->orWhere("'.$field['fieldName'].'", "like", $value)'."\n";
                    }
                }
                $i++;
            }
        }
        $templateData = str_replace('$API_VUEJS_CONTROLLER_FILTER$', $filter, $templateData);
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

        if ($this->commandData->getAddOn('swagger')) {
            $module = Str::snake($this->commandData->commandObj->option('module')) ?: Str::snake(app('modules')->getUsedNow());

            $templateData = str_replace('path="/', 'path="' . $module . '/', $templateData);
        }

        return $templateData;
    }
}