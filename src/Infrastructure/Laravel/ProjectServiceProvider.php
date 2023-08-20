<?php

namespace Project\Infrastructure\Laravel;

use App\Http\Middleware\AssignClientHashCookie;
use Project\Common\CQRS\Buses\CompositeEventBus;
use Project\Common\CQRS\Buses\CompositeRequestBus;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\Environment\EnvironmentInterface;
use Project\Infrastructure\Laravel\Services\FileManager;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Infrastructure\Laravel\Environment\EnvironmentService;
use Project\Infrastructure\Laravel\CQRS\Buses\Decorators\TransactionBus;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\CartServiceProvider;
use Project\Modules\Catalogue\Infrastructure\Laravel\CatalogueServiceProvider;
use Project\Modules\Administrators\Infrastructure\Laravel\AdministratorsServiceProvider;

class ProjectServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private array $providers = [
        CatalogueServiceProvider::class,
        AdministratorsServiceProvider::class,
        CartServiceProvider::class,
    ];

    public $singletons = [
        FileManagerInterface::class => FileManager::class
    ];

    public function register()
    {
        $this->registerProviders();
        $this->registerEnvironment();
        $this->registerAssignClientHashMiddleware();
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
            return new EnvironmentService(
                config('project.application.client-hash-cookie-name')
            );
        });
    }

    private function registerAssignClientHashMiddleware()
    {
        $this->app->singleton(AssignClientHashCookie::class, function ($app) {
            return new AssignClientHashCookie(
                config('project.application.client-hash-cookie-name'),
                config('project.application.client-hash-cookie-lifetime-in-minutes'),
            );
        });
    }

    private function registerBuses()
    {
        $this->app->singleton('CommandBus', function () {
            return new TransactionBus(new CompositeRequestBus);
        });

        $this->app->singleton('QueryBus', function () {
            return new TransactionBus(new CompositeRequestBus);
        });

        $this->app->singleton('EventBus', function () {
            return new CompositeEventBus();
        });

        $this->app->resolving(DispatchEventsInterface::class, function ($object, $app) {
            $object->setDispatcher($app->make('EventBus'));
        });
    }
}