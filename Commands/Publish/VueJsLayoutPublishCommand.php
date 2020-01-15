<?php
/**
 * Created by PhpStorm.
 * User: ahechevarria
 * Date: 17/01/19
 * Time: 10:24
 */

namespace Modules\Generator\Console\Publish;

use InfyOm\Generator\Commands\Publish\VueJsLayoutPublishCommand as InfyOmVueJsLayoutPublishCommand;
use Modules\Generator\Common\Traits\BaseCommandTrait;
use Modules\Generator\Common\Traits\PublishBaseCommandTrait;

class VueJsLayoutPublishCommand extends InfyOmVueJsLayoutPublishCommand
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
    protected $name = 'generate.publish:vuejs';

    /**
     * Overrides handle trait
     */
    public function handle ()
    {
        parent::handle();
    }
}
