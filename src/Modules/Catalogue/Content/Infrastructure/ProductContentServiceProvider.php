<?php

namespace Project\Modules\Catalogue\Content\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Catalogue\Content\Commands;
use Project\Modules\Catalogue\Content\Services\ProductContentServiceInterface;
use Project\Modules\Catalogue\Content\Infrastructure\Laravel\Services\ProductContentService;

class ProductContentServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\UpdateProductContentCommand::class => [ProductContentServiceInterface::class, 'update']
    ];

    private array $queriesMapping = [];
    private array $eventsMapping = [];

    public array $singletons = [
        ProductContentServiceInterface::class => ProductContentService::class
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
    }
}