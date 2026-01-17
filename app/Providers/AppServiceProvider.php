<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register middleware aliases
        $this->app['router']->aliasMiddleware('check.role', \App\Http\Middleware\CheckRole::class);
        $this->app['router']->aliasMiddleware('check.permission', \App\Http\Middleware\CheckPermission::class);
        $this->app['router']->aliasMiddleware('only.panel', \App\Http\Middleware\OnlyPanelAccess::class);
        $this->app['router']->aliasMiddleware('only.client', \App\Http\Middleware\OnlyClientAccess::class);
    }
}
