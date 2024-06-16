<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Repository;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models as Eloquent;

trait FindCartByClientTrait
{
    private function getByClientId(Client $client): ?Eloquent\Cart
    {
        if (empty($client->getId())) {
            return null;
        }

        return Eloquent\Cart::where(['client_id' => $client->getId()])->first();
    }

    private function getByClientHash(Client $client): ?Eloquent\Cart
    {
        return Eloquent\Cart::where(['client_hash' => $client->getHash()])->first();
    }
}