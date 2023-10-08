<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Repository;

use Project\Modules\Client\Api\DTO;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Client\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Client\Repository\QueryClientsRepositoryInterface;
use Project\Modules\Client\Infrastructure\Laravel\Utils\ClientEloquent2DTOConverter;

class QueryClientsEloquentRepository implements QueryClientsRepositoryInterface
{
    public function get(int $id): DTO\Client
    {
        if (!($record = Eloquent\Client::find($id))) {
            throw new NotFoundException('Client does not exists');
        }

        return ClientEloquent2DTOConverter::convert($record);
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\Client::query()
            ->paginate(
                perPage: $limit,
                page: $page,
            );

        $dtos = array_map('ClientEloquent2DTOConverter::convert', $query->items());
        return new PaginatedCollection(
            $dtos,
            new Pagination(
                $query->currentPage(),
                $query->perPage(),
                $query->total()
            )
        );
    }
}