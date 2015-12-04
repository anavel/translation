<?php
namespace ANavallaSuiza\Transleite\Providers;

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
        $this->app->view->composer('transleite::molecules.sidebar.default', 'ANavallaSuiza\Transleite\View\Composers\SidebarComposer');
    }
}
