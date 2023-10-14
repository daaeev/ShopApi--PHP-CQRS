<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel\Repository;

use Project\Modules\Administrators\Api\DTO;
use Project\Common\Repository\NotFoundException;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Modules\Administrators\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Administrators\Repository\QueryAdminsRepositoryInterface;

class QueryAdminsEloquentRepository implements QueryAdminsRepositoryInterface
{
    public function get(int $id): DTO\Admin
    {
        $record = Eloquent\Administrator::query()
            ->where('id', $id)
            ->first();

        if (!$record) {
            throw new NotFoundException('Admin does not exists');
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Administrator $admin): DTO\Admin
    {
        return new DTO\Admin(
            $admin->id,
            $admin->name,
            $admin->login,
            $admin->roles,
        );
    }

    public function list(int $page, int $limit, array $options = []): PaginatedCollection
    {
        $query = Eloquent\Administrator::query()
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