<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Administrators\Commands;
use Project\Modules\Administrators\Queries;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;
use Project\Modules\Administrators\Repository\AdminRepositoryInterface;
use Project\Modules\Administrators\Infrastructure\Laravel\Repository\AdminsRepository;
use Project\Modules\Administrators\Infrastructure\Laravel\AuthManager\GuardAuthManager;

class AdministratorsServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\CreateAdminCommand::class => Commands\Handlers\CreateAdminHandler::class,
        Commands\UpdateAdminCommand::class => Commands\Handlers\UpdateAdminHandler::class,
        Commands\DeleteAdminCommand::class => Commands\Handlers\DeleteAdminHandler::class,
        Commands\AuthorizeCommand::class => Commands\Handlers\AuthorizeHandler::class,
        Commands\LogoutCommand::class => Commands\Handlers\LogoutHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetAdminQuery::class => Queries\Handlers\GetAdminHandler::class,
        Queries\AdminsListQuery::class => Queries\Handlers\AdminsListHandler::class,
        Queries\AuthorizedAdminQuery::class => Queries\Handlers\AuthorizedAdminHandler::class,
    ];
    private array $eventsMapping = [];

    public array $singletons = [
        AdminRepositoryInterface::class => AdminsRepository::class,
        AuthManagerInterface::class => GuardAuthManager::class
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}