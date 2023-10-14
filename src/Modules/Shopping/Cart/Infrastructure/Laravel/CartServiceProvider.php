<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel;

use Project\Modules\Shopping\Cart\Queries;
use Project\Modules\Shopping\Cart\Commands;
use Project\Modules\Shopping\Cart\Consumers;
use Illuminate\Support\ServiceProvider;
use Project\Common\CQRS\Buses\EventBus;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Shopping\Cart\Presenters\CartPresenter;
use Project\Modules\Catalogue\Api\Events\Product as ProductEvents;
use Project\Modules\Shopping\Cart\Presenters\CartPresenterInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Repository\QueryCartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\CartsEloquentRepository;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\QueryCartsEloquentRepository;

class CartServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\AddItemCommand::class => Commands\Handlers\AddItemHandler::class,
        Commands\UpdateItemCommand::class => Commands\Handlers\UpdateItemHandler::class,
        Commands\RemoveItemCommand::class => Commands\Handlers\RemoveItemHandler::class,
        Commands\ChangeCurrencyCommand::class => Commands\Handlers\ChangeCurrencyHandler::class,

        Commands\UsePromocodeCommand::class => Commands\Handlers\UsePromocodeHandler::class,
        Commands\RemovePromocodeCommand::class => Commands\Handlers\RemovePromocodeHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetActiveCartQuery::class => Queries\Handlers\GetActiveCartHandler::class,
    ];

    private array $eventsMapping = [
        ProductEvents\ProductActivityChanged::class => Consumers\ProductDeactivatedConsumer::class,
        ProductEvents\ProductAvailabilityChanged::class => Consumers\ProductDeactivatedConsumer::class,
    ];

    public array $singletons = [
        CartsRepositoryInterface::class => CartsEloquentRepository::class,
        QueryCartsRepositoryInterface::class => QueryCartsEloquentRepository::class,
        CartPresenterInterface::class => CartPresenter::class,
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
        $this->app->get('EventBus')->registerBus(new EventBus($this->eventsMapping, $this->app));
    }
}