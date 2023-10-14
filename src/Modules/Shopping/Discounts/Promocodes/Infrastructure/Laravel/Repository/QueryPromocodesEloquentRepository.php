<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Modules\Shopping\Api\DTO\Promocodes as DTO;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Shopping\Discounts\Promocodes\Utils\PromocodeEntity2DTOConverter;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\QueryPromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\PromocodeEloquent2EntityConverter;

class QueryPromocodesEloquentRepository implements QueryPromocodesRepositoryInterface
{
    public function __construct(
        private PromocodeEloquent2EntityConverter $eloquentConverter
    ) {}

    public function get(int $id, array $options = []): DTO\Promocode
    {
        $record = Eloquent\Promocode::find($id);

        if (empty($record)) {
            throw new NotFoundException('Promocode does not exists');
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Promocode $record): DTO\Promocode
    {
        return PromocodeEntity2DTOConverter::convert(
            $this->eloquentConverter->convert($record)
        );
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\Promocode::query()
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