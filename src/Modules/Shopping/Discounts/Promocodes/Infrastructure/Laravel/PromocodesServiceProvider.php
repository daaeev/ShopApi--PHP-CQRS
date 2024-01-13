<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Shopping\Discounts\Promocodes\Queries;
use Project\Modules\Shopping\Discounts\Promocodes\Commands;
use Project\Modules\Shopping\Discounts\Promocodes\Repository;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository\PromocodesEloquentRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository\QueryPromocodesEloquentRepository;

class PromocodesServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\CreatePromocodeCommand::class => Commands\Handlers\CreatePromocodeHandler::class,
        Commands\UpdatePromocodeCommand::class => Commands\Handlers\UpdatePromocodeHandler::class,
        Commands\DeletePromocodeCommand::class => Commands\Handlers\DeletePromocodeHandler::class,
        Commands\ActivatePromocodeCommand::class => Commands\Handlers\ActivatePromocodeHandler::class,
        Commands\DeactivatePromocodeCommand::class => Commands\Handlers\DeactivatePromocodeHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetPromocodeQuery::class => Queries\Handlers\GetPromocodeHandler::class,
        Queries\GetPromocodesListQuery::class => Queries\Handlers\GetPromocodeListHandler::class,
    ];

    public array $singletons = [
        Repository\PromocodesRepositoryInterface::class => PromocodesEloquentRepository::class,
        Repository\QueryPromocodesRepositoryInterface::class => QueryPromocodesEloquentRepository::class,
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}