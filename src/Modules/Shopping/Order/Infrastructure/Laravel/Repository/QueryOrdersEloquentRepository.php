<?php

namespace Project\Modules\Shopping\Order\Infrastructure\Laravel\Repository;

use Project\Common\Client\Client;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Shopping\Api\DTO\Order as DTO;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Shopping\Order\Infrastructure\Laravel\Eloquent;
use Project\Modules\Shopping\Order\Repository\QueryOrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Infrastructure\Laravel\Utils\OrderEloquentToDTOConverter;

class QueryOrdersEloquentRepository implements QueryOrdersRepositoryInterface
{
    public function get(int|string $id, ?Client $client = null): DTO\Order
    {
        $record = Eloquent\Order::query()
            ->where('id', $id)
            ->client($client?->getId(), $client?->getHash())
            ->first();

        if (empty($record)) {
            throw new NotFoundException("Order #$id does not exists");
        }

        return OrderEloquentToDTOConverter::convert($record);
    }

    public function list(
        int $page = 1,
        int $limit = 15,
        array $filters = [],
        array $sorting = [],
    ): PaginatedCollection {
        $records = Eloquent\Order::query()
            ->with(['offers', 'delivery'])
            ->price($filters['priceFrom'] ?? null, $filters['priceTo'] ?? null)
            ->client($filters['clientId'] ?? null, $filters['clientHash'] ?? null)
            ->phone($filters['phone'] ?? null)
            ->email($filters['email'] ?? null)
            ->name($filters['name'] ?? null)
            ->status($filters['status'] ?? null)
            ->paymentsStatus($filters['paymentsStatus'] ?? null)
            ->order($sorting)
            ->paginate(perPage: $limit, page: $page);

        $converted = array_map(
            fn (Eloquent\Order $record) => OrderEloquentToDTOConverter::convert($record),
            $records->items()
        );

        $pagination = new Pagination($records->currentPage(), $records->perPage(), $records->total());
        return new PaginatedCollection($converted, $pagination);
    }
}