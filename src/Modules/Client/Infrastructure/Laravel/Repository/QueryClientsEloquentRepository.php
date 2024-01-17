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
    public function getById(int $id): DTO\Client
    {
        $record = Eloquent\Client::find($id);
        if (empty($record)) {
            throw new NotFoundException('Client does not exists');
        }

        return ClientEloquent2DTOConverter::convert($record);
    }

    public function getByHash(string $hash): DTO\Client
    {
        $record = Eloquent\Client::query()
            ->where('hash', $hash)
            ->first();

        if (empty($record)) {
            throw new NotFoundException('Client does not exists');
        }

        return ClientEloquent2DTOConverter::convert($record);
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\Client::query()
            ->applyOptions($options)
            ->paginate(
                perPage: $limit,
                page: $page,
            );

        $clientsDTO = array_map(function (Eloquent\Client $record) {
            return ClientEloquent2DTOConverter::convert($record);
        }, $query->items());

        return new PaginatedCollection($clientsDTO, new Pagination(
            $query->currentPage(),
            $query->perPage(),
            $query->total()
        ));
    }
}