<?php

namespace Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Modules\Catalogue\Content\Product\Commands;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Modules\Catalogue\Content\Product\Services\ProductContentServiceInterface;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Services\ProductContentService;

class ProductContentServiceProvider extends ServiceProvider
{
    private array $commandsMapping = [
        Commands\UpdateProductContentCommand::class => [ProductContentServiceInterface::class, 'updateContent'],
        Commands\UpdateProductPreviewCommand::class => [ProductContentServiceInterface::class, 'updatePreview'],
        Commands\AddProductImageCommand::class => [ProductContentServiceInterface::class, 'addImage'],
        Commands\DeleteProductImageCommand::class => [ProductContentServiceInterface::class, 'deleteImage'],
    ];

    public array $singletons = [
        ProductContentServiceInterface::class => ProductContentService::class
    ];

    public function boot()
    {
        $this->app->get('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
    }
}