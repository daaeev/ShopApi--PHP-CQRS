<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Repository;

use Project\Common\Entity\Duration;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Modules\Shopping\Api\DTO\Promotions as DTO;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Shopping\Discounts\Promotions\Repository\QueryPromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Infrastructure\Laravel\Eloquent;

class QueryPromotionsEloquentRepository implements QueryPromotionsRepositoryInterface
{
    public function get(int $id, array $options = []): DTO\Promotion
    {
        $record = Eloquent\Promotion::query()
            ->where('id', $id)
            ->with('discounts')
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Promotion not found');
        }

        return $this->convertPromotionRecord($record);
    }

    private function convertPromotionRecord(Eloquent\Promotion $record): DTO\Promotion
    {
        return new DTO\Promotion(
            $record->id,
            $record->name,
            new Duration(
                $record->start_date
                    ? new \DateTimeImmutable($record->start_date)
                    : null,
                $record->end_date
                    ? new \DateTimeImmutable($record->end_date)
                    : null,
            ),
            $record->status->value,
            array_map(function (Eloquent\PromotionDiscount $discountRecord) {
                return new DTO\PromotionDiscount(
                    $discountRecord->id,
                    $discountRecord->type->value,
                    $discountRecord->data,
                );
            }, $record->discounts->all()),
            new \DateTimeImmutable($record->created_at),
            $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        );
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $records = Eloquent\Promotion::query()
            ->with('discounts')
            ->paginate(perPage: $limit, page: $page);

        $promotionsDTO = array_map([$this, 'convertPromotionRecord'], $records->items());
        return new PaginatedCollection($promotionsDTO, new Pagination(
            $records->currentPage(),
            $records->perPage(),
            $records->total(),
        ));
    }
}