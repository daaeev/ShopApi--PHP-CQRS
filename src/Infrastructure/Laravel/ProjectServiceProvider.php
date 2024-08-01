<?php

namespace Project\Infrastructure\Laravel;

use Psr\Log\LoggerInterface;
use App\Http\Middleware\AssignClientHashCookie;
use Project\Modules\Administrators\Api\AdministratorsApi;
use Project\Common\Services\Cookie\CookieManagerInterface;
use Project\Infrastructure\Laravel\Services\CookieManager;
use Project\Common\Services\Environment\EnvironmentService;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Common\ApplicationMessages\Buses\CompositeEventBus;
use Project\Common\ApplicationMessages\Buses\CompositeRequestBus;
use Project\Common\ApplicationMessages\ApplicationMessagesManager;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Client\Infrastructure\Laravel\ClientsServiceProvider;
use Project\Common\ApplicationMessages\Buses\Decorators\LoggingBusDecorator;
use Project\Modules\Shopping\Infrastructure\Laravel\ShoppingServiceProvider;
use Project\Modules\Catalogue\Infrastructure\Laravel\CatalogueServiceProvider;
use Project\Modules\Administrators\Infrastructure\Laravel\AdministratorsServiceProvider;
use Project\Infrastructure\Laravel\ApplicationMessages\Buses\Decorators\TransactionBusDecorator;

class ProjectServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private array $providers = [
        CatalogueServiceProvider::class,
        AdministratorsServiceProvider::class,
        ShoppingServiceProvider::class,
        ClientsServiceProvider::class,
    ];

    public function register()
    {
        $this->registerProviders();
        $this->registerCookieManager();
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

    private function registerCookieManager()
    {
        $this->app->singleton(CookieManagerInterface::class, CookieManager::class);
    }

    private function registerEnvironment()
    {
        $this->app->singleton(EnvironmentInterface::class, function ($app) {
            return new EnvironmentService(
                $app->make(CookieManagerInterface::class),
                $app->make(AdministratorsApi::class),
                config('project.application.client-hash-cookie-name'),
            );
        });
    }

    private function registerAssignClientHashMiddleware()
    {
        $this->app->singleton(AssignClientHashCookie::class, function ($app) {
            return new AssignClientHashCookie(
                $app->make(CookieManagerInterface::class),
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