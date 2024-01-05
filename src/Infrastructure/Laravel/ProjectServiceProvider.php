<?php

namespace Project\Infrastructure\Laravel;

use Psr\Log\LoggerInterface;
use Project\Modules\Client\Api\ClientsApi;
use App\Http\Middleware\AssignClientHashCookie;
use Project\Common\Events\DispatchEventsInterface;
use Project\Common\CQRS\ApplicationMessagesManager;
use Project\Common\CQRS\Buses\Decorators\LoggingBusDecorator;
use Project\Common\Environment\EnvironmentInterface;
use Project\Common\CQRS\Buses\CompositeEventBus;
use Project\Common\CQRS\Buses\CompositeRequestBus;
use Project\Infrastructure\Laravel\Environment\EnvironmentService;
use Project\Infrastructure\Laravel\CQRS\Buses\Decorators\TransactionBusDecorator;
use Project\Modules\Client\Infrastructure\Laravel\ClientServiceProvider;
use Project\Modules\Shopping\Infrastructure\Laravel\ShoppingServiceProvider;
use Project\Modules\Catalogue\Infrastructure\Laravel\CatalogueServiceProvider;
use Project\Modules\Administrators\Infrastructure\Laravel\AdministratorsServiceProvider;

class ProjectServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private array $providers = [
        CatalogueServiceProvider::class,
        AdministratorsServiceProvider::class,
        ShoppingServiceProvider::class,
        ClientServiceProvider::class,
    ];

    public function register()
    {
        $this->registerProviders();
        $this->registerEnvironment();
        $this->registerAssignClientHashMiddleware();
        $this->registerBuses();
        $this->registerMessageManager();
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
                $app->make(ClientsApi::class),
                config('project.application.client-hash-cookie-name'),
                config('project.application.client-hash-cookie-length'),
            );
        });
    }

    private function registerAssignClientHashMiddleware()
    {
        $this->app->singleton(AssignClientHashCookie::class, function ($app) {
            return new AssignClientHashCookie(
                config('project.application.client-hash-cookie-name'),
                config('project.application.client-hash-cookie-lifetime-in-minutes'),
                config('project.application.client-hash-cookie-length'),
            );
        });
    }

    private function registerBuses()
    {
        $this->app->singleton('CommandBus', function () {
            return new LoggingBusDecorator(
                new TransactionBusDecorator(new CompositeRequestBus),
                $this->app->make(LoggerInterface::class)
            );
        });

        $this->app->singleton('QueryBus', function () {
            return new LoggingBusDecorator(
                new TransactionBusDecorator(new CompositeRequestBus),
                $this->app->make(LoggerInterface::class),
            );
        });

        $this->app->singleton('EventBus', function () {
            return new LoggingBusDecorator(
                new TransactionBusDecorator(new CompositeEventBus()),
                $this->app->make(LoggerInterface::class),
            );
        });

        $this->app->resolving(DispatchEventsInterface::class, function ($object, $app) {
            $object->setDispatcher($app->make('EventBus'));
        });
    }

    public function registerMessageManager()
    {
        $this->app->singleton(ApplicationMessagesManager::class, function ($app) {
            return new ApplicationMessagesManager(
                $app->make('CommandBus'),
                $app->make('QueryBus'),
                $app->make('EventBus'),
            );
        });
    }
}