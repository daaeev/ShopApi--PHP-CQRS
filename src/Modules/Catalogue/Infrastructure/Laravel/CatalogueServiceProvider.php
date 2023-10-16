<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Catalogue\Queries;
use Project\Modules\Catalogue\Presenters\ProductPresenterInterface;
use Project\Modules\Catalogue\Presenters\CategoryPresenterInterface;
use Project\Modules\Catalogue\Product\Queries\Handlers\GetProductHandler;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;
use Project\Modules\Catalogue\Categories\Queries\Handlers\GetCategoryHandler;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\ProductServiceProvider;
use Project\Modules\Catalogue\Infrastructure\Laravel\Presenters\ProductAllContentEloquentPresenter;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\CategoriesServiceProvider;
use Project\Modules\Catalogue\Infrastructure\Laravel\Presenters\CategoryAllContentEloquentPresenter;
use Project\Modules\Catalogue\Infrastructure\Laravel\Repositories\QueryCatalogueEloquentRepository;
use Project\Modules\Catalogue\Settings\Infrastructure\Laravel\CatalogueSettingsServiceProvider;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\ProductContentServiceProvider;
use Project\Modules\Catalogue\Content\Category\Infrastructure\Laravel\CategoryContentServiceProvider;

class CatalogueServiceProvider extends ServiceProvider
{
    private array $providers = [
        ProductServiceProvider::class,
        ProductContentServiceProvider::class,
        CategoriesServiceProvider::class,
        CategoryContentServiceProvider::class,
        CatalogueSettingsServiceProvider::class
    ];

    private array $queriesMapping = [
        Queries\ProductDetailsQuery::class => Queries\Handlers\ProductDetailsHandler::class,
        Queries\ProductsListQuery::class => Queries\Handlers\ProductsListHandler::class,
    ];

    public array $singletons = [
        QueryCatalogueRepositoryInterface::class => QueryCatalogueEloquentRepository::class,
    ];

    public function register()
    {
        $this->registerProviders();
        $this->registerPresenters();
    }

    private function registerPresenters()
    {
        $this->app->when(GetCategoryHandler::class)
            ->needs(CategoryPresenterInterface::class)
            ->give(CategoryAllContentEloquentPresenter::class);

        $this->app->when(GetProductHandler::class)
            ->needs(ProductPresenterInterface::class)
            ->give(ProductAllContentEloquentPresenter::class);
    }

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    public function boot()
    {
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}