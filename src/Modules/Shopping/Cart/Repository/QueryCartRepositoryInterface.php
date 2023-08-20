<?php

namespace Project\Modules\Shopping\Cart\Repository;

use Project\Modules\Shopping\Cart\Api\DTO;
use Project\Common\Environment\Client\Client;

interface QueryCartRepositoryInterface
{
    public function getActiveCart(Client $client): DTO\Cart;
}