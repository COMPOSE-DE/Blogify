<?php

namespace Donatix\Blogify;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Donatix\Blogify\Services\Validation;

class BlogifyServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $providers = [
        'Collective\Html\HtmlServiceProvider',
        'Intervention\Image\ImageServiceProvider',
        'jorenvanhocht\Tracert\TracertServiceProvider'
    ];

    /**
     * @var array
     */
    protected $aliases = [
        'Tracert' => 'Donatix\Blogify\Facades\Tracert',
        'Form' => 'Collective\Html\FormFacade',
        'Html' => 'Collective\Html\HtmlFacade',
        'Image' => 'Intervention\Image\Facades\Image',
        'Input' => 'Illuminate\Support\Facades\Input',
    ];

    /**
     * Register the service provider
     */
    public function register()
    {
        $this->app->bind('donatix.blogify', function()
        {
            $db = $this->app['db'];
            $config = $this->app['config'];
            return new Blogify($db, $config);
        });

        $this->registerMiddleware();
        $this->registerServiceProviders();
        $this->registerAliases();
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

    /**
     * @return void
     */
    private function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('BlogifyAdminAuthenticate', 'Donatix\Blogify\Middleware\BlogifyAdminAuthenticate');
        $this->app['router']->aliasMiddleware('BlogifyVerifyCsrfToken', 'Donatix\Blogify\Middleware\BlogifyVerifyCsrfToken');
        $this->app['router']->aliasMiddleware('CanEditPost', 'Donatix\Blogify\Middleware\CanEditPost');
        $this->app['router']->aliasMiddleware('DenyIfBeingEdited', 'Donatix\Blogify\Middleware\DenyIfBeingEdited');
        $this->app['router']->aliasMiddleware('BlogifyGuest', 'Donatix\Blogify\Middleware\Guest');
        $this->app['router']->aliasMiddleware('HasAdminOrAuthorRole', 'Donatix\Blogify\Middleware\HasAdminOrAuthorRole');
        $this->app['router']->aliasMiddleware('HasAdminRole', 'Donatix\Blogify\Middleware\HasAdminRole');
        $this->app['router']->aliasMiddleware('RedirectIfAuthenticated', 'Donatix\Blogify\Middleware\RedirectIfAuthenticated');
        $this->app['router']->aliasMiddleware('IsOwner', 'Donatix\Blogify\Middleware\IsOwner');
        $this->app['router']->aliasMiddleware('CanViewPost', 'Donatix\Blogify\Middleware\CanViewPost');
        $this->app['router']->aliasMiddleware('ProtectedPost', 'Donatix\Blogify\Middleware\ProtectedPost');
        $this->app['router']->aliasMiddleware('ConfirmPasswordChange', 'Donatix\Blogify\Middleware\ConfirmPasswordChange');
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
            'Donatix\Blogify\Commands\BlogifyMigrateCommand',
            'Donatix\Blogify\Commands\BlogifySeedCommand',
            'Donatix\Blogify\Commands\BlogifyGeneratePublicPartCommand',
            'Donatix\Blogify\Commands\BlogifyCreateRequiredDirectories',
        ]);
    }

}
