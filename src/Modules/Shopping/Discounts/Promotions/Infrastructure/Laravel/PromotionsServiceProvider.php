<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Shopping\Discounts\Promotions\Queries;
use Project\Modules\Shopping\Discounts\Promotions\Commands;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Repository\QueryPromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\HandlerFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\MechanicFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\HandlerFactoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\Factory\MechanicFactoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Repository\PromotionsEloquentRepository;
use Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Repository\QueryPromotionsEloquentRepository;

class PromotionsServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\CreatePromotionCommand::class => Commands\Handlers\CreatePromotionHandler::class,
        Commands\UpdatePromotionCommand::class => Commands\Handlers\UpdatePromotionHandler::class,
        Commands\DeletePromotionCommand::class => Commands\Handlers\DeletePromotionHandler::class,
        Commands\AddPromotionDiscountCommand::class => Commands\Handlers\AddPromotionDiscountHandler::class,
        Commands\RemovePromotionDiscountCommand::class => Commands\Handlers\RemovePromotionDiscountHandler::class,
        Commands\DisablePromotionCommand::class => Commands\Handlers\DisablePromotionHandler::class,
        Commands\EnablePromotionCommand::class => Commands\Handlers\EnablePromotionHandler::class,
    ];

    private array $queriesMapping = [
        Queries\GetPromotionQuery::class => Queries\Handlers\GetPromotionHandler::class,
        Queries\GetPromotionsQuery::class => Queries\Handlers\GetPromotionsHandler::class,
    ];

    public array $singletons = [
        PromotionsRepositoryInterface::class => PromotionsEloquentRepository::class,
        QueryPromotionsRepositoryInterface::class => QueryPromotionsEloquentRepository::class,
        MechanicFactoryInterface::class => MechanicFactory::class,
        HandlerFactoryInterface::class => HandlerFactory::class,
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}