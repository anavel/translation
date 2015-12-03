<?php
namespace ANavallaSuiza\Transleite;

use ANavallaSuiza\Adoadomin\Support\ModuleProvider;
use Request;

class TransleiteModuleProvider extends ModuleProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'transleite');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'transleite');

//        $this->publishes([
//            __DIR__.'/../public/js' => public_path('vendor/transleite/js'),
//        ], 'assets');

        $this->publishes([
            __DIR__.'/../config/transleite.php' => config_path('transleite.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

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
        return config('transleite.name');
    }

    public function routes()
    {
        return __DIR__.'/Http/routes.php';
    }

    public function mainRoute()
    {
        return route('transleite.home');
    }

    public function hasSidebar()
    {
        return true;
    }

    public function sidebarMenu()
    {
        return 'transleite::molecules.sidebar.default';
    }

    public function isActive()
    {
        $uri = Request::route()->uri();

        if (strpos($uri, 'transleite') !== false) {
            return true;
        }

        return false;
    }
}
