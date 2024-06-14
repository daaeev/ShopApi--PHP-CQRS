<?php

namespace Project\Modules\Shopping\Order\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Shopping\Presenters\OrderPresenter;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Shopping\Order\Commands;
use Project\Modules\Shopping\Order\Queries;
use Project\Modules\Shopping\Presenters\OrderPresenterInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Repository\QueryOrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Infrastructure\Laravel\Repository\OrdersEloquentRepository;
use Project\Modules\Shopping\Order\Infrastructure\Laravel\Repository\QueryOrdersEloquentRepository;

class OrdersServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\CreateOrderCommand::class => Commands\Handlers\CreateOrderHandler::class,
        Commands\UpdateOrderCommand::class => Commands\Handlers\UpdateOrderHandler::class,
        Commands\DeleteOrderCommand::class => Commands\Handlers\DeleteOrderHandler::class,

        Commands\AddOfferCommand::class => Commands\Handlers\AddOfferHandler::class,
        Commands\UpdateOfferCommand::class => Commands\Handlers\UpdateOfferHandler::class,
        Commands\RemoveOfferCommand::class => Commands\Handlers\RemoveOfferHandler::class,

        Commands\AddPromoCommand::class => Commands\Handlers\AddPromoHandler::class,
        Commands\RemovePromoCommand::class => Commands\Handlers\RemovePromoHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetOrderQuery::class => Queries\Handlers\GetOrderHandler::class,
        Queries\GetOrdersQuery::class => Queries\Handlers\GetOrdersHandler::class,
    ];

    public array $singletons = [
        OrdersRepositoryInterface::class => OrdersEloquentRepository::class,
        QueryOrdersRepositoryInterface::class => QueryOrdersEloquentRepository::class,
        OrderPresenterInterface::class => OrderPresenter::class,
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}