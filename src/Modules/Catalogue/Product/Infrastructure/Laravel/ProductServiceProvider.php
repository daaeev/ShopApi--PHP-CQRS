<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Catalogue\Product\Queries;
use Project\Modules\Catalogue\Product\Commands;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Catalogue\Product\Repository\ProductApiRepositoryInterface;
use Project\Modules\Catalogue\Product\Repository\QueryProductRepositoryInterface;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository\ProductRepository;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository\ProductApiRepository;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository\QueryProductRepository;

class ProductServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\CreateProductCommand::class => Commands\Handlers\CreateProductHandler::class,
        Commands\UpdateProductCommand::class => Commands\Handlers\UpdateProductHandler::class,
        Commands\DeleteProductCommand::class => Commands\Handlers\DeleteProductHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetProductQuery::class => Queries\Handlers\GetProductHandler::class,
        Queries\ProductsListQuery::class => Queries\Handlers\ProductsListHandler::class,
    ];

    private array $eventsMapping = [];

    public array $singletons = [
        ProductRepositoryInterface::class => ProductRepository::class,
        ProductApiRepositoryInterface::class => ProductApiRepository::class,
        QueryProductRepositoryInterface::class => QueryProductRepository::class,
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}