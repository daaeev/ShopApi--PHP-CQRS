<?php

namespace Project\Infrastructure\Laravel;

use Project\Common\CQRS\Buses\ChainBus;
use Project\Common\CQRS\Buses\ChainEventBus;
use Project\Infrastructure\Laravel\CQRS\Buses\Decorators\TransactionChainBus;
use Project\Modules\Test\Infrastructure\Laravel\TestServiceProvider;

class ProjectServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private array $providers = [
        TestServiceProvider::class,
    ];
    public function register()
    {
        $this->registerProviders();
        $this->registerBuses();
    }

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    private function registerBuses()
    {
        $this->app->singleton('CommandBus', function () {
            return new TransactionChainBus(new ChainBus());
        });
        $this->app->singleton('QueryBus', function () {
            return new TransactionChainBus(new ChainBus());
        });
        $this->app->singleton('EventBus', function () {
            return new ChainEventBus();
        });
    }
}