<?php

namespace Project\Modules\Shopping\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\CartServiceProvider;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\PromocodesServiceProvider;

class ShoppingServiceProvider extends ServiceProvider
{
    private array $providers = [
        CartServiceProvider::class,
        PromocodesServiceProvider::class,
    ];

    public function register()
    {
        $this->registerProviders();
    }

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}