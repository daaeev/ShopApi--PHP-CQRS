<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Modules\Shopping\Api\DTO\Promocodes as DTO;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\QueryPromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Models as Eloquent;

class QueryPromocodesEloquentRepository implements QueryPromocodesRepositoryInterface
{
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
        return new DTO\Promocode(
            $record->id,
            $record->name,
            $record->code,
            $record->discount_percent,
            $record->active,
            new \DateTimeImmutable($record->start_date),
            $record->end_date
                ? new \DateTimeImmutable($record->end_date)
                : null,
            new \DateTimeImmutable($record->created_at),
            $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
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