<?php

namespace Project\Modules\Shopping\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\CartServiceProvider;

class ShoppingServiceProvider extends ServiceProvider
{
    private array $providers = [
        CartServiceProvider::class,
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