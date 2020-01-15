<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 16/01/19
 * Time: 18:21
 */

namespace Modules\Generator\Console\Publish;

use InfyOm\Generator\Commands\Publish\PublishTemplateCommand as InfyOmPublishTemplateCommand;
use Modules\Generator\Common\Traits\BaseCommandTrait;
use Modules\Generator\Common\Traits\PublishBaseCommandTrait;

class PublishTemplateCommand extends InfyOmPublishTemplateCommand
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
    protected $name = 'generate.publish:templates';

    /**
     * Overrides handle trait
     */
    public function handle ()
    {
        parent::handle();
    }
}
