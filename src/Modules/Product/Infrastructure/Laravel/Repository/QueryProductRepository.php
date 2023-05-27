<?php

namespace Project\Modules\Product\Infrastructure\Laravel\Repository;

use Project\Modules\Product\Api\DTO;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Product\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Product\Repository\QueryProductsRepositoryInterface;

class QueryProductRepository implements QueryProductsRepositoryInterface
{
    public function get(int $id): DTO\Product
    {
        $record = Eloquent\Product::query()
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
            array_map(function (array $color) {
                return new DTO\Color(
                    $color['value'],
                    $color['type'],
                );
            }, $product->colors),
            $product->sizes,
            array_map(function (array $price) {
                return new DTO\Price(
                    $price['currency'],
                    $price['value'],
                );
            }, $product->prices),
        );
    }

    public function list(int $page, int $limit, array $params = []): PaginatedCollection
    {
        $query = Eloquent\Product::query()
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