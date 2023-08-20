<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\CQRS\Buses\RequestBus;

class PromocodesServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [];
    private array $queriesMapping = [];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}