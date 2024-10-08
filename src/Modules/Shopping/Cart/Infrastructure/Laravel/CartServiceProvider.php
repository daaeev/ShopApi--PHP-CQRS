<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Shopping\Cart\Queries;
use Project\Modules\Shopping\Cart\Commands;
use Project\Modules\Shopping\Cart\Consumers;
use Project\Common\ApplicationMessages\Buses\EventBus;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Shopping\Cart\Presenters\CartPresenter;
use Project\Modules\Catalogue\Api\Events\Product\ProductEvent;
use Project\Common\ApplicationMessages\Events\RegisteredConsumer;
use Project\Modules\Shopping\Cart\Presenters\CartPresenterInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Repository\QueryCartsRepositoryInterface;
use Project\Modules\Shopping\Adapters\Events\ProductDeactivatedDeserializer;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\CartsEloquentRepository;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository\QueryCartsEloquentRepository;

class CartServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\AddOfferCommand::class => Commands\Handlers\AddOfferHandler::class,
        Commands\UpdateOfferCommand::class => Commands\Handlers\UpdateOfferHandler::class,
        Commands\RemoveOfferCommand::class => Commands\Handlers\RemoveOfferHandler::class,
        Commands\ChangeCurrencyCommand::class => Commands\Handlers\ChangeCurrencyHandler::class,

        Commands\UsePromocodeCommand::class => Commands\Handlers\UsePromocodeHandler::class,
        Commands\RemovePromocodeCommand::class => Commands\Handlers\RemovePromocodeHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetCartQuery::class => Queries\Handlers\GetCartHandler::class,
    ];

    public array $singletons = [
        CartsRepositoryInterface::class => CartsEloquentRepository::class,
        QueryCartsRepositoryInterface::class => QueryCartsEloquentRepository::class,
        CartPresenterInterface::class => CartPresenter::class,
    ];

    private function getEventsMapping(): array
    {
        $productDeactivatedConsumer = new RegisteredConsumer(
            Consumers\ProductDeactivatedConsumer::class,
            ProductDeactivatedDeserializer::class
        );

        return [
            ProductEvent::ACTIVITY_CHANGED->value => $productDeactivatedConsumer,
            ProductEvent::AVAILABILITY_CHANGED->value => $productDeactivatedConsumer,
        ];
    }

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
        $this->app->get('EventBus')->registerBus(new EventBus($this->getEventsMapping(), $this->app));
    }
}