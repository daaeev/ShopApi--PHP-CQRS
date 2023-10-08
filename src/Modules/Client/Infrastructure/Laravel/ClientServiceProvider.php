<?php

namespace Project\Modules\Client\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Client\Queries;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Client\Repository;
use Project\Modules\Client\Infrastructure\Laravel\Repository\ClientsEloquentRepository;
use Project\Modules\Client\Infrastructure\Laravel\Repository\QueryClientsEloquentRepository;

class ClientServiceProvider extends ServiceProvider
{
    private array $queriesMapping = [
        Queries\GetClientQuery::class => Queries\Handlers\GetClientHandler::class,
        Queries\GetClientsQuery::class => Queries\Handlers\GetClientsHandler::class,
    ];

    public array $singletons = [
        Repository\ClientsRepositoryInterface::class => ClientsEloquentRepository::class,
        Repository\QueryClientsRepositoryInterface::class => QueryClientsEloquentRepository::class,
    ];

    public function boot()
    {
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}