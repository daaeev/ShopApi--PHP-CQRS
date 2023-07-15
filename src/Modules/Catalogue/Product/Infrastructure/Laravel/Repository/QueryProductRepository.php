<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository;

use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Product\Repository\QueryProductRepositoryInterface;

class QueryProductRepository implements QueryProductRepositoryInterface
{
    public function get(int $id, array $options = []): DTO\Product
    {
        $record = Eloquent\Product::query()
            ->with('prices', 'sizes', 'colors')
            ->where('id', $id)
            ->first();

        if (!$record) {
            throw new NotFoundException('Product does not exists');
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Product $product): DTO\Product
    {
        return new DTO\Product(
            $product->id,
            $product->name,
            $product->code,
            $product->active,
            $product->availability,
            array_column($product->colors->all(), 'color'),
            array_column($product->sizes->all(), 'size'),
            array_map(function (Eloquent\Price $price) {
                return new DTO\Price(
                    $price->currency,
                    $price->price,
                );
            }, $product->prices->all()),
        );
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\Product::query()
            ->with('prices', 'sizes', 'colors')
            ->paginate(
                $limit,
                ['*'],
                'page',
                $page
            );

        $items = array_map([$this, 'hydrate'], $query->items());
        return new PaginatedCollection($items, new Pagination(
            $query->currentPage(),
            $query->perPage(),
            $query->total()
        ));
    }
}