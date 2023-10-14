<?php

namespace Project\Modules\Administrators\Repository;

use Project\Modules\Administrators\Api\DTO;
use Project\Common\Entity\Collections\PaginatedCollection;

interface QueryAdminsRepositoryInterface
{
    public function get(int $id): DTO\Admin;

    public function list(int $page, int $limit, array $options = []): PaginatedCollection;
}