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
        // Register repositories
        $this->app->bind(
            \App\Repositories\BusinessRepositoryInterface::class,
            \App\Repositories\BusinessRepository::class,
        );
        $this->app->bind(
            \App\Repositories\ClientRepositoryInterface::class,
            \App\Repositories\ClientRepository::class,
        );
        $this->app->bind(
            \App\Repositories\ServiceRepositoryInterface::class,
            \App\Repositories\ServiceRepository::class,
        );
        $this->app->bind(
            \App\Repositories\LocationRepositoryInterface::class,
            \App\Repositories\LocationRepository::class,
        );
        $this->app->bind(
            \App\Repositories\MasterRepositoryInterface::class,
            \App\Repositories\MasterRepository::class,
        );
        $this->app->bind(
            \App\Repositories\AppointmentRepositoryInterface::class,
            \App\Repositories\AppointmentRepository::class,
        );
        $this->app->bind(
            \App\Repositories\TelegramUserStateRepositoryInterface::class,
            \App\Repositories\TelegramUserStateRepository::class,
        );

        $this->app->bind(
            \App\Contracts\PaymentGatewayInterface::class,
            \App\Services\BepaidService::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register middleware aliases
        $this->app["router"]->aliasMiddleware(
            "check.role",
            \App\Http\Middleware\CheckRole::class,
        );
        $this->app["router"]->aliasMiddleware(
            "check.permission",
            \App\Http\Middleware\CheckPermission::class,
        );
        $this->app["router"]->aliasMiddleware(
            "only.panel",
            \App\Http\Middleware\OnlyPanelAccess::class,
        );
        $this->app["router"]->aliasMiddleware(
            "only.client",
            \App\Http\Middleware\OnlyClientAccess::class,
        );

        // Register model observers
        \App\Models\Master::observe(\App\Observers\MasterObserver::class);
        \App\Models\Subscription::observe(
            \App\Observers\SubscriptionObserver::class,
        );
    }
}
