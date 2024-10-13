<?php

namespace Project\Infrastructure\Laravel;

use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\Config;
use Project\Common\Commands\SendSmsCommand;
use Project\Common\Commands\Handlers\SendSmsHandler;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Administrators\Api\AdministratorsApi;
use Project\Common\Services\Cookie\CookieManagerInterface;
use Project\Infrastructure\Laravel\Services\CookieManager;
use Project\Common\Services\Environment\EnvironmentService;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Common\ApplicationMessages\Buses\CompositeEventBus;
use Project\Common\ApplicationMessages\Buses\CompositeRequestBus;
use Project\Common\ApplicationMessages\ApplicationMessagesManager;
use Project\Infrastructure\Laravel\Middleware\AssignClientHashCookie;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Client\Infrastructure\Laravel\ClientsServiceProvider;
use Project\Common\ApplicationMessages\Buses\Decorators\LoggingBusDecorator;
use Project\Modules\Shopping\Infrastructure\Laravel\ShoppingServiceProvider;
use Project\Infrastructure\Laravel\ApplicationMessages\Buses\QueueCommandBus;
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

    private array $commonCommands = [
        SendSmsCommand::class => SendSmsHandler::class
    ];

    public function register(): void
    {
        $this->registerConfiguration();
        $this->registerProviders();
        $this->registerCookieManager();
        $this->registerEnvironment();
        $this->registerAssignClientHashMiddleware();
        $this->registerBuses();
        $this->registerMessageManager();
        $this->registerCommonCommands();
    }

    private function registerConfiguration(): void
    {
        Config::set('project.application', require __DIR__ . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'application.php');
        Config::set('project.storage', require __DIR__ . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'storage.php');
    }

    private function registerProviders(): void
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    private function registerCookieManager(): void
    {
        $this->app->singleton(CookieManagerInterface::class, CookieManager::class);
    }

    private function registerEnvironment(): void
    {
        $this->app->singleton(EnvironmentInterface::class, function ($app) {
            return new EnvironmentService(
                $app->make(CookieManagerInterface::class),
                $app->make(AdministratorsApi::class),
                config('project.application.client-hash-cookie-name'),
            );
        });
    }

    private function registerAssignClientHashMiddleware(): void
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

    private function registerBuses(): void
    {
        $this->app->singleton('CommandBus', function () {
            return new LoggingBusDecorator(
                new TransactionBusDecorator(new CompositeRequestBus),
                $this->app->make(LoggerInterface::class)
            );
        });

        $this->app->singleton('QueueCommandBus', QueueCommandBus::class);

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

    private function registerMessageManager(): void
    {
        $this->app->singleton(ApplicationMessagesManager::class, function ($app) {
            return new ApplicationMessagesManager(
                $app->make('CommandBus'),
                $app->make('QueueCommandBus'),
                $app->make('QueryBus'),
                $app->make('EventBus'),
            );
        });
    }

    private function registerCommonCommands(): void
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commonCommands, $this->app));
    }
}