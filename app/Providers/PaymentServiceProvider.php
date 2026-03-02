<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Services\GatewayManager;
use App\Services\PaymentHandlers\SubscriptionPaymentHandler;
use App\Services\PaymentService;
use App\Services\PaymentSettingsService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем GatewayManager как singleton
        $this->app->singleton(GatewayManager::class, function ($app) {
            return new GatewayManager;
        });

        // Регистрируем PaymentSettingsService как singleton
        $this->app->singleton(PaymentSettingsService::class, function ($app) {
            return new PaymentSettingsService(
                $app->make(GatewayManager::class)
            );
        });

        // Регистрируем PaymentService как singleton
        $this->app->singleton(PaymentService::class, function ($app) {
            $service = new PaymentService(
                $app->make(GatewayManager::class),
                $app->make(PaymentSettingsService::class)
            );

            // Регистрируем обработчики типов оплат
            $this->registerPaymentHandlers($service);

            return $service;
        });

        // Для обратной совместимости: PaymentGatewayInterface -> BepaidGateway через GatewayManager
        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            return $app->make(GatewayManager::class)->getDefault();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Зарегистрировать обработчики типов оплат
     */
    protected function registerPaymentHandlers(PaymentService $service): void
    {
        // Регистрируем обработчик подписок
        $service->registerHandler(
            $this->app->make(SubscriptionPaymentHandler::class)
        );

        // Здесь можно добавить другие обработчики:
        // $service->registerHandler($this->app->make(PurchasePaymentHandler::class));
        // $service->registerHandler($this->app->make(DonationPaymentHandler::class));
        // $service->registerHandler($this->app->make(BalancePaymentHandler::class));
    }
}
