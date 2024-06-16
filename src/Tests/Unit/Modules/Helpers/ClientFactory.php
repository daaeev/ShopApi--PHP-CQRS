<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Client\Entity;

trait ClientFactory
{
    private function generateClient(): Entity\Client
    {
        $client = new Entity\Client(Entity\ClientId::random());
        $client->flushEvents();
        return $client;
    }

    private function makeClient(Entity\ClientId $id): Entity\Client
    {
        return new Entity\Client($id);
    }
}