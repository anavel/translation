<?php
namespace Anavel\Translation\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->view->composer('anavel-translation::molecules.sidebar.default', 'Anavel\Translation\View\Composers\SidebarComposer');
    }
}
