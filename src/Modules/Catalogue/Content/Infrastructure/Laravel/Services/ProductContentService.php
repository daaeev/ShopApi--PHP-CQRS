<?php

namespace Project\Modules\Catalogue\Content\Infrastructure\Laravel\Services;

use Project\Common\Repository\NotFoundException;
use Project\Modules\Catalogue\Content\Commands\UpdateProductContentCommand;
use Project\Modules\Catalogue\Content\Services\ProductContentServiceInterface;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models\Product as EloquentProduct;
use Project\Modules\Catalogue\Content\Infrastructure\Laravel\Models\Content as EloquentContent;

class ProductContentService implements ProductContentServiceInterface
{
    public function update(UpdateProductContentCommand $command): void
    {
        $productExists = EloquentProduct::query()
            ->where('id', $command->product)
            ->exists();

        if (!$productExists) {
            throw new NotFoundException('Product does not exists');
        }

        EloquentContent::updateOrCreate(
            [
                'id' => $command->product,
                'language' => $command->language
            ],
            $command->fields
        );
    }
}