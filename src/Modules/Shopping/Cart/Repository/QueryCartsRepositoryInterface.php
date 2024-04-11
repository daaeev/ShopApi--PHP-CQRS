<?php

namespace Project\Modules\Shopping\Cart\Repository;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;

interface QueryCartsRepositoryInterface
{
    public function getActiveCart(Client $client): DTO\Cart;
}