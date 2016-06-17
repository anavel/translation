<?php
namespace Anavel\Translation;

use Anavel\Foundation\Support\ModuleProvider;
use Request;

class TranslationModuleProvider extends ModuleProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'anavel-translation');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'anavel-translation');

//        $this->publishes([
//            __DIR__.'/../public/js' => public_path('vendor/anavel-translation/js'),
//        ], 'assets');

        $this->publishes([
            __DIR__.'/../config/anavel-translation.php' => config_path('anavel-translation.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/anavel-translation.php', 'anavel-translation');

        $this->app->register('Anavel\Translation\Providers\ViewComposersServiceProvider');
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

    public function name()
    {
        return config('anavel-translation.name');
    }

    public function routes()
    {
        return __DIR__.'/Http/routes.php';
    }

    public function mainRoute()
    {
        return route('anavel-translation.home');
    }

    public function hasSidebar()
    {
        return true;
    }

    public function sidebarMenu()
    {
        return 'anavel-translation::molecules.sidebar.default';
    }

    public function isActive()
    {
        $uri = Request::route()->uri();

        if (strpos($uri, 'translation') !== false) {
            return true;
        }

        return false;
    }
}
