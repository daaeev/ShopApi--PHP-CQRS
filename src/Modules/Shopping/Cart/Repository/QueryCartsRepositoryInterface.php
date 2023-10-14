<?php

namespace Project\Modules\Shopping\Cart\Repository;

use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Common\Environment\Client\Client;

interface QueryCartsRepositoryInterface
{
    public function getActiveCart(Client $client): DTO\Cart;
}