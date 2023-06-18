<?php

namespace Project\Infrastructure\Laravel;

use Project\Common\CQRS\Buses\CompositeBus;
use Project\Common\CQRS\Buses\CompositeEventBus;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Infrastructure\Laravel\Environment\EnvironmentService;
use Project\Modules\Product\Infrastructure\Laravel\ProductServiceProvider;
use Project\Infrastructure\Laravel\CQRS\Buses\Decorators\TransactionCompositeBus;
use Project\Modules\Administrators\Infrastructure\Laravel\AdministratorsServiceProvider;

class ProjectServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private array $providers = [
        ProductServiceProvider::class,
        AdministratorsServiceProvider::class,
    ];

    public function register()
    {
        $this->registerProviders();
        $this->registerEnvironment();
        $this->registerBuses();
    }

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    private function registerEnvironment()
    {
        $this->app->singleton(EnvironmentInterface::class, function ($app) {
            return new EnvironmentService();
        });
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

        $this->app->resolving(DispatchEventsInterface::class, function ($object, $app) {
            $object->setDispatcher($app->make('EventBus'));
        });
    }
}