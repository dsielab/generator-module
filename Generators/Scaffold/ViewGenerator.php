<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 21:04
 */

namespace Modules\Generator\Generators\Scaffold;

use File;
use Illuminate\Support\Str;
use InfyOm\Generator\Generators\Scaffold\ViewGenerator as InfyOmViewGenerator;
use Modules\Generator\Common\CommandData;

class ViewGenerator extends InfyOmViewGenerator
{
    /** @var string  */
    protected $path;

    /** @var CommandData  */
    protected $commandData;

    /** @var string  */
    protected $templateType;

    /**
     * ViewGenerator constructor.
     * @param CommandData $commandData
     */
    public function __construct(CommandData $commandData)
    {
        parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $commandData->config->pathViews;
        $this->templateType = config('modules.generator.templates', 'adminlte-templates');
    }

    /**
     * Overriding rollback default behavior
     * @param array $views
     */
    public function rollback($views = [])
    {
        parent::rollback($views);

        $directoryName = Str::replaceFirst(base_path().'/', '', $this->path);

        if (count(File::allFiles($this->path))
            && !$this->commandData->commandObj->confirm(
            "\nDirectory [$directoryName] is not empty. Do you want to remove it as well?", false
            )
        ) {
            return;
        }

        File::deleteDirectory($this->path);
        $this->commandData->commandComment("Directory [$directoryName] deleted!");
    }
}
