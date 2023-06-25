<?php

namespace Project\Modules\Cart\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Cart\Commands;
use Project\Modules\Cart\Queries;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Cart\Infrastructure\Laravel\Repository\CartRepository;

class CartServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\AddItemCommand::class => Commands\Handlers\AddItemHandler::class,
        Commands\UpdateItemCommand::class => Commands\Handlers\UpdateItemHandler::class,
        Commands\RemoveItemCommand::class => Commands\Handlers\RemoveItemHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetActiveCartQuery::class => Queries\Handlers\GetActiveCartHandler::class,
    ];

    private array $eventsMapping = [];

    public array $singletons = [
        CartRepositoryInterface::class => CartRepository::class
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}