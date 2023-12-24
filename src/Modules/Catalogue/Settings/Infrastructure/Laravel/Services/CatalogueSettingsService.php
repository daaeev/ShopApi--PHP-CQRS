<?php

namespace Project\Modules\Catalogue\Settings\Infrastructure\Laravel\Services;

use Project\Common\Repository\NotFoundException;
use Project\Modules\Catalogue\Settings\Commands\UpdateProductSettingsCommand;
use Project\Modules\Catalogue\Settings\Services\CatalogueSettingsServiceInterface;
use Project\Modules\Catalogue\Settings\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models\Product as EloquentProduct;

class CatalogueSettingsService implements CatalogueSettingsServiceInterface
{
    public function update(UpdateProductSettingsCommand $command): void
    {
        $this->guardProductExists($command->product);
        Eloquent\Settings::updateOrCreate(
            ['product' => $command->product],
            ['displayed' => $command->displayed]
        );
    }

    private function guardProductExists(int $product): void
    {
        $productExists = EloquentProduct::query()
            ->where('id', $product)
            ->exists();

        if (!$productExists) {
            throw new NotFoundException('Product does not exists');
        }
    }
}