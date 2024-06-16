<?php

namespace Project\Tests\Unit\Modules\Client\Entity;

use Project\Modules\Client\Entity;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Client\Api\Events\ClientCreated;
use Project\Tests\Unit\Modules\Helpers\ClientFactory;

class CreateClientTest extends \PHPUnit\Framework\TestCase
{
    use ClientFactory, AssertEvents;

    public function testCreate()
    {
        $client = $this->makeClient($id = Entity\ClientId::random());

        $this->assertTrue($id->equalsTo($client->getId()));
        $this->assertNull($client->getName()->getFirstName());
        $this->assertNull($client->getName()->getLastName());
        $this->assertNull($client->getName()->getFullName());
        $this->assertFalse($client->getContacts()->isPhoneConfirmed());
        $this->assertFalse($client->getContacts()->isEmailConfirmed());
        $this->assertNull($client->getContacts()->getPhone());
        $this->assertNull($client->getContacts()->getEmail());
        $this->assertNotEmpty($client->getCreatedAt());
        $this->assertNull($client->getUpdatedAt());
        $this->assertEvents($client, [new ClientCreated($client)]);
    }
}