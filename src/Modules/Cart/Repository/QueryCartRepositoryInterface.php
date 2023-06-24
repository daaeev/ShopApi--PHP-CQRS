<?php

namespace Project\Modules\Cart\Repository;

use Project\Modules\Cart\Api\DTO;
use Project\Common\Environment\Client\Client;

interface QueryCartRepositoryInterface
{
    public function getActiveCart(Client $client): DTO\Cart;
}