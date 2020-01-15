<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 16/01/19
 * Time: 18:20
 */

namespace Modules\Generator\Console\Publish;

use InfyOm\Generator\Commands\Publish\LayoutPublishCommand as InfyOmLayoutPublishCommand;
use Modules\Generator\Common\Traits\BaseCommandTrait;
use Modules\Generator\Common\Traits\PublishBaseCommandTrait;

class LayoutPublishCommand extends InfyOmLayoutPublishCommand
{
    /**
     * Modifying Inheritance for InfyOm BaseCommand class to inject module param
     */
    use BaseCommandTrait;

    /**
     * Modifying Inheritance for InfyOm PublishBaseCommand class to override ConfigGenerator
     */
    use PublishBaseCommandTrait {
        handle as public handleTrait;
    }

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate.publish:layout';

    /**
     * @throws \Exception
     */
    public function handle ()
    {
        $this->handleTrait();

        $templatesPath = config(
            'modules.generator.path.templates_dir',
            resource_path('templates/module-generator-templates/')
        ).'scaffold/layouts/app.stub';

        if (!file_exists($templatesPath)) {
            $this->publishScaffoldTemplates();
        }

        parent::handle();
    }

    /**
     * Publishes scaffold templates.
     */
    public function publishScaffoldTemplates()
    {
        $templateType = config('modules.generator.templates', 'core-templates');

        $templatesPath = base_path('vendor/dsielab/'.$templateType.'/templates/scaffold');

        return $this->publishDirectory($templatesPath, $this->templatesDir.'scaffold', 'module-generator-templates/scaffold', true);
    }
}
