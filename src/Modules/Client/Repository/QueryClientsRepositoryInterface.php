<?php

namespace Project\Modules\Client\Repository;

use Project\Modules\Client\Api\DTO;
use Project\Common\Entity\Collections\PaginatedCollection;

interface QueryClientsRepositoryInterface
{
    public function getById(int $id): DTO\Client;

    public function getByHash(string $hash): DTO\Client;

    public function list(int $page, int $limit, array $options = []): PaginatedCollection;
}