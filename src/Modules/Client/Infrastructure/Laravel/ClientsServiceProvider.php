<?php

namespace Project\Modules\Client\Infrastructure\Laravel;

use Project\Modules\Client\Queries;
use Project\Modules\Client\Consumers;
use Project\Modules\Client\Repository;
use Illuminate\Support\ServiceProvider;
use Project\Common\ApplicationMessages\Buses\EventBus;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Shopping\Api\Events\Orders\OrderEvent;
use Project\Common\ApplicationMessages\Events\RegisteredConsumer;
use Project\Modules\Client\Adapters\Events\OrderCompletedDeserializer;
use Project\Modules\Client\Infrastructure\Laravel\Repository\ClientsEloquentRepository;
use Project\Modules\Client\Infrastructure\Laravel\Repository\QueryClientsEloquentRepository;

class ClientsServiceProvider extends ServiceProvider
{
    private array $queriesMapping = [
        Queries\GetClientQuery::class => Queries\Handlers\GetClientHandler::class,
        Queries\GetClientsQuery::class => Queries\Handlers\GetClientsHandler::class,
    ];

    public array $singletons = [
        Repository\ClientsRepositoryInterface::class => ClientsEloquentRepository::class,
        Repository\QueryClientsRepositoryInterface::class => QueryClientsEloquentRepository::class,
    ];

    private function getEventsMapping(): array
    {
        return [
            OrderEvent::COMPLETED->value => new RegisteredConsumer(
                Consumers\OrderCompletedConsumer::class,
                OrderCompletedDeserializer::class
            )
        ];
    }

    public function boot()
    {
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
        $this->app->get('EventBus')->registerBus(new EventBus($this->getEventsMapping(), $this->app));
    }
}