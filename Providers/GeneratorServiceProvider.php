<?php

namespace Modules\Generator\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Generator\Console\API\APIControllerGeneratorCommand;
use Modules\Generator\Console\API\APIRequestsGeneratorCommand;
use Modules\Generator\Console\API\TestsGeneratorCommand;
use Modules\Generator\Console\Common\MigrationGeneratorCommand;
use Modules\Generator\Console\Common\ModelGeneratorCommand;
use Modules\Generator\Console\API\APIGeneratorCommand;
use Modules\Generator\Console\APIScaffoldGeneratorCommand;
use Modules\Generator\Console\Common\RepositoryGeneratorCommand;
use Modules\Generator\Console\Module\ModuleMakeCommand;
use Modules\Generator\Console\Module\ProviderMakeCommand;
use Modules\Generator\Console\Module\UnUseCommand;
use Modules\Generator\Console\Module\UseCommand;
use Modules\Generator\Console\Module\UsedCommand;
use Modules\Generator\Console\Publish\GeneratorPublishCommand;
use Modules\Generator\Console\Publish\LayoutPublishCommand;
use Modules\Generator\Console\Publish\PublishTemplateCommand;
use Modules\Generator\Console\Publish\VueJsLayoutPublishCommand;
use Modules\Generator\Console\RollbackGeneratorCommand;
use Modules\Generator\Console\Scaffold\ControllerGeneratorCommand;
use Modules\Generator\Console\Scaffold\RequestsGeneratorCommand;
use Modules\Generator\Console\Scaffold\ScaffoldGeneratorCommand;
use Modules\Generator\Console\Scaffold\ViewsGeneratorCommand;
use Modules\Generator\Console\VueJs\VueJsGeneratorCommand;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton('generate.module', function ($app) {
            return new ModuleMakeCommand();
        });

        $this->app->singleton('generate.module-use', function ($app) {
            return new UseCommand();
        });

        $this->app->singleton('generate.module-used', function ($app) {
            return new UsedCommand();
        });

        $this->app->singleton('generate.module-unuse', function ($app) {
            return new UnUseCommand();
        });

        $this->app->singleton('generate.module-provider', function ($app) {
            return new ProviderMakeCommand();
        });

        $this->app->singleton('generate.publish', function ($app) {
            return new GeneratorPublishCommand();
        });

        $this->app->singleton('generate.api', function ($app) {
            return new APIGeneratorCommand();
        });

        $this->app->singleton('generate.scaffold', function ($app) {
            return new ScaffoldGeneratorCommand();
        });

        $this->app->singleton('generate.publish.layout', function ($app) {
            return new LayoutPublishCommand();
        });

        $this->app->singleton('generate.publish.templates', function ($app) {
            return new PublishTemplateCommand();
        });

        $this->app->singleton('generate.api_scaffold', function ($app) {
            return new APIScaffoldGeneratorCommand();
        });

        $this->app->singleton('generate.migration', function ($app) {
            return new MigrationGeneratorCommand();
        });

        $this->app->singleton('generate.model', function ($app) {
            return new ModelGeneratorCommand();
        });

        $this->app->singleton('generate.repository', function ($app) {
            return new RepositoryGeneratorCommand();
        });

        $this->app->singleton('generate.api.controller', function ($app) {
            return new APIControllerGeneratorCommand();
        });

        $this->app->singleton('generate.api.requests', function ($app) {
            return new APIRequestsGeneratorCommand();
        });

        $this->app->singleton('generate.api.tests', function ($app) {
            return new TestsGeneratorCommand();
        });

        $this->app->singleton('generate.scaffold.controller', function ($app) {
            return new ControllerGeneratorCommand();
        });

        $this->app->singleton('generate.scaffold.requests', function ($app) {
            return new RequestsGeneratorCommand();
        });

        $this->app->singleton('generate.scaffold.views', function ($app) {
            return new ViewsGeneratorCommand();
        });

        $this->app->singleton('generate.rollback', function ($app) {
            return new RollbackGeneratorCommand();
        });

        $this->app->singleton('generate.vuejs', function ($app) {
            return new VueJsGeneratorCommand();
        });

        $this->app->singleton('generate.publish.vuejs', function ($app) {
            return new VueJsLayoutPublishCommand();
        });

        $this->commands([
            'generate.module',
            'generate.module-use',
            'generate.module-used',
            'generate.module-unuse',
            'generate.module-provider',
            'generate.publish',
            'generate.api',
            'generate.scaffold',
            'generate.api_scaffold',
            'generate.publish.layout',
            'generate.publish.templates',
            'generate.migration',
            'generate.model',
            'generate.repository',
            'generate.api.controller',
            'generate.api.requests',
            'generate.api.tests',
            'generate.scaffold.controller',
            'generate.scaffold.requests',
            'generate.scaffold.views',
            'generate.rollback',
            'generate.vuejs',
            'generate.publish.vuejs',
        ]);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('modules/generator.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'module.generator'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/generator');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/generator';
        }, \Config::get('view.paths')), [$sourcePath]), 'generator');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/generator');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'generator');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'generator');
        }
    }

    /**
     * Register an additional directory of factories.
     * 
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
