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
    use PublishBaseCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate.publish:layout';
}