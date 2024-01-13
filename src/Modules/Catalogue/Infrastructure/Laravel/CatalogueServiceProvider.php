<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel;

use Project\Modules\Catalogue\Queries;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Project\Common\Services\FileManager\FileManager;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Infrastructure\Laravel\Services\LaravelStorage;
use Project\Common\Services\FileManager\FileManagerInterface;
use Project\Modules\Catalogue\Presenters\ProductPresenterInterface;
use Project\Modules\Catalogue\Presenters\CategoryPresenterInterface;
use Project\Modules\Catalogue\Product\Queries\Handlers\GetProductHandler;
use Project\Modules\Catalogue\Repositories\QueryCatalogueRepositoryInterface;
use Project\Modules\Catalogue\Categories\Queries\Handlers\GetCategoryHandler;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\ProductServiceProvider;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\CategoriesServiceProvider;
use Project\Common\Services\FileManager\DirectoryNameGenerators\BaseDirectoryNameGenerator;
use Project\Common\Services\FileManager\FileNameGenerators\TimestampPrefixFileNameGenerator;
use Project\Modules\Catalogue\Infrastructure\Laravel\Converters\CatalogueEloquent2DTOConverter;
use Project\Modules\Catalogue\Settings\Infrastructure\Laravel\CatalogueSettingsServiceProvider;
use Project\Modules\Catalogue\Infrastructure\Laravel\Presenters\ProductAllContentEloquentPresenter;
use Project\Modules\Catalogue\Infrastructure\Laravel\Repositories\QueryCatalogueEloquentRepository;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\ProductContentServiceProvider;
use Project\Modules\Catalogue\Infrastructure\Laravel\Presenters\CategoryAllContentEloquentPresenter;
use Project\Modules\Catalogue\Content\Product\Infrastructure\Laravel\Services\ProductContentService;
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
        $this->registerFileManager();
        $this->registerPresenters();
    }

    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    private function registerFileManager()
    {
        $services = [
            CatalogueEloquent2DTOConverter::class,
            ProductContentService::class,
            ProductAllContentEloquentPresenter::class,
        ];

        $this->app
            ->when($services)
            ->needs(FileManagerInterface::class)
            ->give(function ($app) {
                $dir = config('project.storage.products-images');
                return new FileManager(
                    new BaseDirectoryNameGenerator($dir),
                    new TimestampPrefixFileNameGenerator,
                    new LaravelStorage(Storage::build([
                        'driver' => 'local',
                        'root' => Storage::drive('public')->path(''),
                        'url' => Storage::drive('public')->url(''),
                        'visibility' => Filesystem::VISIBILITY_PUBLIC
                    ]))
                );
            });
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

    public function boot()
    {
        $this->app->get('QueryBus')->registerBus(new RequestBus($this->queriesMapping, $this->app));
    }
}