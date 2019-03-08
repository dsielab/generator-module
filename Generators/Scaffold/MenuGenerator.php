<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 18/01/19
 * Time: 21:29
 */

namespace Modules\Generator\Generators\Scaffold;

use Illuminate\Support\Str;
use InfyOm\Generator\Generators\Scaffold\MenuGenerator as InfyOmMenuGenerator;
use InfyOm\Generator\Utils\FileUtil;
use Modules\Generator\Common\CommandData;

class MenuGenerator extends InfyOmMenuGenerator
{
    /** @var string */
    protected $path;

    /** @var CommandData  */
    protected $commandData;

    /** @var string  */
    protected $templateType;

    /** @var string */
    protected $menuContents;

    /** @var string */
    protected $menuTemplate;

    /**
     * MenuGenerator constructor.
     * @param CommandData $commandData
     */
    public function __construct(CommandData $commandData)
    {
        parent::__construct($commandData);

        $this->commandData = $commandData;
        $configBasePath = config(
            'infyom.laravel_generator.path.views',
            base_path('resources/views/'
            )
        );
        $menuFile = $commandData->getAddOn('menu.menu_file');
        $this->path = $configBasePath.$menuFile;

        // Overriding default template definition
        $this->templateType = config('modules.generator.templates', 'adminlte-templates');

        // Creating empty menu blade template if not exists
        if (!file_exists($this->path)) {
            FileUtil::createFile($configBasePath, $menuFile, '');

            $this->commandData->commandInfo($menuFile.' created');
        }

        $this->menuContents = file_get_contents($this->path);

        $this->menuTemplate = get_template('scaffold.layouts.menu_template', $this->templateType);

        $this->menuTemplate = fill_template($this->commandData->dynamicVars, $this->menuTemplate);
    }

    /**
     * Overriding generate function to avoid private path property error
     */
    public function generate()
    {
        $this->menuContents .= $this->menuTemplate.infy_nl();

        file_put_contents($this->path, $this->menuContents);
        $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' menu added.');
    }

    public function rollback()
    {
        if (Str::contains($this->menuContents, $this->menuTemplate)) {
            file_put_contents($this->path, str_replace($this->menuTemplate, '', $this->menuContents));
            $this->commandData->commandComment('menu deleted');
        }
    }
}