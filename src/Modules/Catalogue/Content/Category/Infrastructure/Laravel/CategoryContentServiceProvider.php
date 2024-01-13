<?php

namespace Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Catalogue\Content\Category\Commands\UpdateCategoryContentCommand;
use Project\Modules\Catalogue\Content\Category\Services\CategoryContentServiceInterface;
use Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel\Services\CategoryContentService;

class CategoryContentServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        UpdateCategoryContentCommand::class => [CategoryContentServiceInterface::class, 'updateContent'],
    ];

    private array $queriesMapping = [];
    private array $eventsMapping = [];

    public array $singletons = [
        CategoryContentServiceInterface::class => CategoryContentService::class
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
    }
}