<?php

namespace Project\Modules\Catalogue\Categories\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Catalogue\Categories\Commands;
use Project\Modules\Catalogue\Categories\Queries;
use Project\Modules\Catalogue\Categories\Repository\CategoriesRepositoryInterface;
use Project\Modules\Catalogue\Categories\Repository\QueryCategoriesRepositoryInterface;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Repository\CategoriesEloquentRepository;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Repository\QueryCategoriesEloquentRepository;

class CategoriesServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\CreateCategoryCommand::class => Commands\Handlers\CreateCategoryHandler::class,
        Commands\UpdateCategoryCommand::class => Commands\Handlers\UpdateCategoryHandler::class,
        Commands\DeleteCategoryCommand::class => Commands\Handlers\DeleteCategoryHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetCategoryQuery::class => Queries\Handlers\GetCategoryHandler::class,
        Queries\CategoriesListQuery::class => Queries\Handlers\CategoriesListHandler::class,
    ];

    private array $eventsMapping = [];

    public array $singletons = [
        CategoriesRepositoryInterface::class => CategoriesEloquentRepository::class,
        QueryCategoriesRepositoryInterface::class => QueryCategoriesEloquentRepository::class,
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}