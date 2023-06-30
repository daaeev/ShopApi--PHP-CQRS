<?php

namespace Project\Modules\Catalogue\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\ProductServiceProvider;
use Project\Modules\Catalogue\Content\Product\Infrastructure\ProductContentServiceProvider;

class CatalogueServiceProvider extends ServiceProvider
{
    private array $providers = [
        ProductServiceProvider::class,
        ProductContentServiceProvider::class,
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