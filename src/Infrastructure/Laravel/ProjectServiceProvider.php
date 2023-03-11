<?php

namespace Project\Infrastructure\Laravel;

use Project\Common\CQRS\Buses\CompositeBus;
use Project\Common\CQRS\Buses\CompositeEventBus;
use Project\Infrastructure\Laravel\CQRS\Buses\Decorators\TransactionCompositeBus;
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
            return new TransactionCompositeBus(new CompositeBus());
        });
        $this->app->singleton('QueryBus', function () {
            return new TransactionCompositeBus(new CompositeBus());
        });
        $this->app->singleton('EventBus', function () {
            return new CompositeEventBus();
        });
    }
}