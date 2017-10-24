<?php

namespace ComposeDe\Blogify;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use ComposeDe\Blogify\Services\Validation;

class BlogifyServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $providers = [
        'Collective\Html\HtmlServiceProvider',
        'Intervention\Image\ImageServiceProvider',
    ];

    /**
     * @var array
     */
    protected $aliases = [
        'Form' => 'Collective\Html\FormFacade',
        'Html' => 'Collective\Html\HtmlFacade',
        'Image' => 'Intervention\Image\Facades\Image',
        'Input' => 'Illuminate\Support\Facades\Input',
        'BlogifyAuth' => \ComposeDe\Blogify\Facades\BlogifyAuth::class,
        'BlogifyRole' => \ComposeDe\Blogify\Facades\BlogifyRole::class,
    ];

    /**
     * Register the service provider
     */
    public function register()
    {
        $this->app->bind('ComposeDe.blogify', function()
        {
            $db = $this->app['db'];
            $config = $this->app['config'];
            return new Blogify($db, $config);
        });

        $this->bindModels();
        $this->registerMiddleware();
        $this->registerServiceProviders();
        $this->registerAliases();
        $this->registerAuthWrapper();
    }

    /**
     * Load the resources
     *
     */
    public function boot()
    {
        // Load the routes for the package
        include __DIR__.'/routes.php';

        $this->publish();

        $this->loadViewsFrom(__DIR__.'/../views', 'blogify');
        $this->loadViewsFrom(__DIR__.'/../Example/Views', 'blogifyPublic');

        // Make the config file accessible even when the files are not published
        $this->mergeConfigFrom(__DIR__.'/../config/blogify.php', 'blogify');

        $this->loadTranslationsFrom(__DIR__.'/../lang/', 'blogify');

        $this->registerCommands();

        // Register the class that serves extra validation rules
        $this->app['validator']->resolver(
            function(
                $translator,
                $data,
                $rules,
                $messages = array(),
                $customAttributes = array()
            ) {
            return new Validation($translator, $data, $rules, $messages, $customAttributes);
        });
    }

    ///////////////////////////////////////////////////////////////////////////
    // Helper methods
    ///////////////////////////////////////////////////////////////////////////

    private function bindModels()
    {
        $this->bindModelIfNeeded(\App\User::class, 'blogify.models.user');
        $this->bindModelIfNeeded(\ComposeDe\Blogify\Models\Category::class);
        $this->bindModelIfNeeded(\ComposeDe\Blogify\Models\Comment::class);
        $this->bindModelIfNeeded(\ComposeDe\Blogify\Models\Media::class);
        $this->bindModelIfNeeded(\ComposeDe\Blogify\Models\Post::class);
        $this->bindModelIfNeeded(\ComposeDe\Blogify\Models\Role::class);
        $this->bindModelIfNeeded(\ComposeDe\Blogify\Models\Status::class);
        $this->bindModelIfNeeded(\ComposeDe\Blogify\Models\Tag::class);
        $this->bindModelIfNeeded(\ComposeDe\Blogify\Models\Visibility::class);
    }


    private function bindModelIfNeeded($className, $configKey = null)
    {
        if($configKey === null) {
            $classBaseName = class_basename($className);
            $configKey = 'blogify.models.' . snake_case($classBaseName);
        }

        $configValue = config($configKey, $className);

        if($configValue != $className) {
            $this->app->bind($className, function () use ($configValue) {
                return $this->app->make($configValue);
            });
        }
    }



    /**
     * @return void
     */
    private function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('BlogifyAdminAuthenticate', 'ComposeDe\Blogify\Middleware\BlogifyAdminAuthenticate');
        $this->app['router']->aliasMiddleware('BlogifyVerifyCsrfToken', 'ComposeDe\Blogify\Middleware\BlogifyVerifyCsrfToken');
        $this->app['router']->aliasMiddleware('CanEditPost', 'ComposeDe\Blogify\Middleware\CanEditPost');
        $this->app['router']->aliasMiddleware('DenyIfBeingEdited', 'ComposeDe\Blogify\Middleware\DenyIfBeingEdited');
        $this->app['router']->aliasMiddleware('BlogifyGuest', 'ComposeDe\Blogify\Middleware\Guest');
        $this->app['router']->aliasMiddleware('HasAdminOrAuthorRole', 'ComposeDe\Blogify\Middleware\HasAdminOrAuthorRole');
        $this->app['router']->aliasMiddleware('HasAdminRole', 'ComposeDe\Blogify\Middleware\HasAdminRole');
        $this->app['router']->aliasMiddleware('RedirectIfAuthenticated', 'ComposeDe\Blogify\Middleware\RedirectIfAuthenticated');
        $this->app['router']->aliasMiddleware('IsOwner', 'ComposeDe\Blogify\Middleware\IsOwner');
        $this->app['router']->aliasMiddleware('CanViewPost', 'ComposeDe\Blogify\Middleware\CanViewPost');
        $this->app['router']->aliasMiddleware('ConfirmPasswordChange', 'ComposeDe\Blogify\Middleware\ConfirmPasswordChange');
    }

    /**
     * @return void
     */
    private function registerServiceProviders()
    {
        foreach ($this->providers as $provider)
        {
            $this->app->register($provider);
        }
    }

    /**
     * @return void
     */
    private function registerAliases()
    {
        $loader = AliasLoader::getInstance();

        foreach ($this->aliases as $key => $alias)
        {
            $loader->alias($key, $alias);
        }

        $customModels = config('blogify.models');

        foreach($customModels as $key => $model)
        {
            $loader->alias('Blogify' . studly_case($key) . 'Model', $model);
        }
    }

    private function registerAuthWrapper()
    {
        $this->app->bind('ComposeDe.blogifyAuth', function () {
            $configValue = config('blogify.auth_wrapper', \ComposeDe\Helpers\BlogifyAuth::class);

            return $this->app->make($configValue);
        });
    }


    /**
     * @return void
     */
    private function publish()
    {
        // Publish the config files for the package
        $this->publishes([
            __DIR__.'/../config' => config_path('/'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../public/assets' => base_path('/public/assets/blogify/'),
            __DIR__.'/../public/ckeditor' => base_path('public/ckeditor/'),
            __DIR__.'/../public/datetimepicker' => base_path('public/datetimepicker/')
        ], 'assets');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/blogify'),
        ]);
    }

    private function registerCommands()
    {
        $this->commands([
            'ComposeDe\Blogify\Commands\BlogifyMigrateCommand',
            'ComposeDe\Blogify\Commands\BlogifySeedCommand',
            'ComposeDe\Blogify\Commands\BlogifyGeneratePublicPartCommand',
            'ComposeDe\Blogify\Commands\BlogifyCreateRequiredDirectories',
        ]);
    }

}
