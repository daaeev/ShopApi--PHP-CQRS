<?php

namespace Project\Modules\Catalogue\Settings\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Catalogue\Settings\Commands;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Catalogue\Settings\Services\CatalogueSettingsServiceInterface;
use Project\Modules\Catalogue\Settings\Infrastructure\Laravel\Services\CatalogueSettingsService;

class CatalogueSettingsServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\UpdateProductSettingsCommand::class => [CatalogueSettingsServiceInterface::class, 'update'],
    ];

    public array $singletons = [
        CatalogueSettingsServiceInterface::class => CatalogueSettingsService::class
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
    }
}