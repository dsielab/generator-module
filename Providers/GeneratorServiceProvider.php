<?php

namespace Modules\Generator\Providers;

use Illuminate\Support\Facades\Config;
use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Generator\Console\API\APIControllerGeneratorCommand;
use Modules\Generator\Console\API\APIRequestsGeneratorCommand;
use Modules\Generator\Console\API\TestsGeneratorCommand;
use Modules\Generator\Console\BulkScaffoldGeneratorCommand;
use Modules\Generator\Console\Common\MigrationGeneratorCommand;
use Modules\Generator\Console\Common\ModelGeneratorCommand;
use Modules\Generator\Console\API\APIGeneratorCommand;
use Modules\Generator\Console\APIScaffoldGeneratorCommand;
use Modules\Generator\Console\Common\RepositoryGeneratorCommand;
use Modules\Generator\Console\DatabaseModelsGeneratorCommand;
use Modules\Generator\Console\Module\ModuleMakeCommand;
use Modules\Generator\Console\Module\ProviderMakeCommand;
use Modules\Generator\Console\Module\UnUseCommand;
use Modules\Generator\Console\Module\UseCommand;
use Modules\Generator\Console\Module\UsedCommand;
use Modules\Generator\Console\Publish\GeneratorPublishCommand;
use Modules\Generator\Console\Publish\LayoutPublishCommand;
use Modules\Generator\Console\Publish\PublishTemplateCommand;
use Modules\Generator\Console\RollbackGeneratorCommand;
use Modules\Generator\Console\Scaffold\ControllerGeneratorCommand;
use Modules\Generator\Console\Scaffold\RequestsGeneratorCommand;
use Modules\Generator\Console\Scaffold\ScaffoldGeneratorCommand;
use Modules\Generator\Console\Scaffold\ViewsGeneratorCommand;

class GeneratorServiceProvider extends ServiceProvider
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

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
        $this->registerSwaggerModifications();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton('generate.module', function () {
            return new ModuleMakeCommand();
        });

        $this->app->singleton('generate.module-use', function () {
            return new UseCommand();
        });

        $this->app->singleton('generate.module-used', function () {
            return new UsedCommand();
        });

        $this->app->singleton('generate.module-unuse', function () {
            return new UnUseCommand();
        });

        $this->app->singleton('generate.module-provider', function () {
            return new ProviderMakeCommand();
        });

        $this->app->singleton('generate.publish', function () {
            return new GeneratorPublishCommand();
        });

        $this->app->singleton('generate.api', function () {
            return new APIGeneratorCommand();
        });

        $this->app->singleton('generate.scaffold', function () {
            return new ScaffoldGeneratorCommand();
        });

        $this->app->singleton('generate.publish.layout', function () {
            return new LayoutPublishCommand();
        });

        $this->app->singleton('generate.publish.templates', function () {
            return new PublishTemplateCommand();
        });

        $this->app->singleton('generate.api_scaffold', function () {
            return new APIScaffoldGeneratorCommand();
        });

        $this->app->singleton('generate.migration', function () {
            return new MigrationGeneratorCommand();
        });

        $this->app->singleton('generate.model', function () {
            return new ModelGeneratorCommand();
        });

        $this->app->singleton('generate.repository', function () {
            return new RepositoryGeneratorCommand();
        });

        $this->app->singleton('generate.api.controller', function () {
            return new APIControllerGeneratorCommand();
        });

        $this->app->singleton('generate.api.requests', function () {
            return new APIRequestsGeneratorCommand();
        });

        $this->app->singleton('generate.api.tests', function () {
            return new TestsGeneratorCommand();
        });

        $this->app->singleton('generate.scaffold.controller', function () {
            return new ControllerGeneratorCommand();
        });

        $this->app->singleton('generate.scaffold.requests', function () {
            return new RequestsGeneratorCommand();
        });

        $this->app->singleton('generate.scaffold.views', function () {
            return new ViewsGeneratorCommand();
        });

        $this->app->singleton('generate.rollback', function () {
            return new RollbackGeneratorCommand();
        });

        $this->app->singleton('generate.bulk_scaffold', function () {
            return new BulkScaffoldGeneratorCommand();
        });

        $this->app->singleton('generate.models_file', function () {
            return new DatabaseModelsGeneratorCommand();
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
            'generate.bulk_scaffold',
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
            'generate.models_file'
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
            __DIR__.'/../Config/config.php', 'modules.generator'
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
        }, Config::get('view.paths')), [$sourcePath]), 'generator');
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

    /**
     * Perform modifications on swagger routes for generated modules
     */
    public function registerSwaggerModifications()
    {
        try {
            $allModules = $this->app->modules->all();
            $appDir = ['app'];

            foreach ($allModules as $moduleName => $module) {
                if (strpos(self::class, $moduleName)) {
                    continue;
                }
                $appDir[] = str_replace('{Module}', $moduleName, 'modules/{Module}/Http/Controllers/API');
                $appDir[] = str_replace('{Module}', $moduleName, 'modules/{Module}/Entities');
            }

            $this->app->config->set('swaggervel.app-dir', $appDir);
        } catch (Exception $e) {
            $this->app->log->error($e);
        }
    }
}
