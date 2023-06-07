<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\CQRS\Buses\RequestBus;

class AdministratorsServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [];
    private array $queriesMapping = [];
    private array $eventsMapping = [];
    public array $singletons = [];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}