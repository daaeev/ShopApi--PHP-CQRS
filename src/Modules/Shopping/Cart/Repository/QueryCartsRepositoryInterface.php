<?php

namespace Project\Modules\Shopping\Cart\Repository;

use Project\Common\Services\Environment\Client;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;

interface QueryCartsRepositoryInterface
{
    public function get(Client $client): DTO\Cart;
}