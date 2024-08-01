<?php

namespace Project\Modules\Shopping\Order\Repository;

use Project\Common\Services\Environment\Client;
use Project\Modules\Shopping\Api\DTO\Order as DTO;
use Project\Common\Entity\Collections\PaginatedCollection;

interface QueryOrdersRepositoryInterface
{
    public function get(int|string $id, ?Client $client = null): DTO\Order;

    public function list(int $page = 1, int $limit = 15, array $filters = [], array $sorting = []): PaginatedCollection;
}