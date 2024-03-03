<?php

namespace Project\Tests\Unit\Modules\Client\Entity;

use Project\Modules\Client\Entity\Name;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Client\Api\Events\ClientUpdated;
use Project\Tests\Unit\Modules\Helpers\ClientFactory;

class ClientNameTest extends \PHPUnit\Framework\TestCase
{
    use ClientFactory, AssertEvents;

    public function testUpdate()
    {
        $client = $this->generateClient();
        $name = new Name('firstName', 'lastName');
        $client->updateName($name);

        $this->assertTrue($name->equalsTo($client->getName()));
        $this->assertNotEmpty($client->getUpdatedAt());
        $this->assertEvents($client, [new ClientUpdated($client)]);
    }

    public function testUpdateToSame()
    {
        $client = $this->generateClient();
        $name = new Name('firstName', 'lastName',);
        $client->updateName($name);
        $client->flushEvents();
        $updatedAt = $client->getUpdatedAt();

        $client->updateName($name);

        $this->assertSame($updatedAt, $client->getUpdatedAt());
        $this->assertEvents($client, []);
    }
}
