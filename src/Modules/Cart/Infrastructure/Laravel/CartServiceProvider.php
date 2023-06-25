<?php

namespace Project\Modules\Cart\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Cart\Commands;
use Project\Modules\Cart\Queries;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Cart\Repository\QueryCartRepositoryInterface;
use Project\Modules\Cart\Infrastructure\Laravel\Repository\CartRepository;
use Project\Modules\Cart\Infrastructure\Laravel\Repository\QueryCartRepository;

class CartServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\AddItemCommand::class => Commands\Handlers\AddItemHandler::class,
        Commands\UpdateItemCommand::class => Commands\Handlers\UpdateItemHandler::class,
        Commands\RemoveItemCommand::class => Commands\Handlers\RemoveItemHandler::class,
        Commands\ChangeCurrencyCommand::class => Commands\Handlers\ChangeCurrencyHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetActiveCartQuery::class => Queries\Handlers\GetActiveCartHandler::class,
    ];

    private array $eventsMapping = [];

    public array $singletons = [
        CartRepositoryInterface::class => CartRepository::class,
        QueryCartRepositoryInterface::class => QueryCartRepository::class
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}