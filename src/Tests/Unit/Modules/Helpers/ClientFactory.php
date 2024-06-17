<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Client\Entity;

trait ClientFactory
{
    use ContactsGenerator;

    private function generateClient(): Entity\Client
    {
        $client = new Entity\Client(Entity\ClientId::random(), $this->generatePhone());
        $client->flushEvents();
        return $client;
    }

    private function makeClient(Entity\ClientId $id, string $phone): Entity\Client
    {
        return new Entity\Client($id, $phone);
    }
}