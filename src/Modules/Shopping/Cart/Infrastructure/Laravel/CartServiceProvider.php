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
use Project\Modules\Shopping\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Shopping\Cart\Repository\QueryCartRepositoryInterface;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\CartRepository;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\QueryCartRepository;

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

    private array $eventsMapping = [
        ProductEvents\ProductActivityChanged::class => Consumers\ProductDeactivatedConsumer::class,
        ProductEvents\ProductAvailabilityChanged::class => Consumers\ProductDeactivatedConsumer::class,
    ];

    public array $singletons = [
        CartRepositoryInterface::class => CartRepository::class,
        QueryCartRepositoryInterface::class => QueryCartRepository::class,
        CartPresenterInterface::class => CartPresenter::class,
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
        $this->app->get('EventBus')->registerBus(new EventBus($this->eventsMapping, $this->app));
    }
}