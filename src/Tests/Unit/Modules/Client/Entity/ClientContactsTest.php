<?php

namespace Project\Tests\Unit\Modules\Client\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ClientFactory;
use Project\Modules\Client\Api\Events\Client\ClientUpdated;

class ClientContactsTest extends \PHPUnit\Framework\TestCase
{
    use ClientFactory, AssertEvents;

    public function testUpdateEmail()
    {
        $client = $this->generateClient();
        $client->updateEmail($email = $this->generateEmail());
        $this->assertEquals($email, $client->getContacts()->getEmail());
        $this->assertNotEmpty($client->getUpdatedAt());
        $this->assertEvents($client, [new ClientUpdated($client)]);
    }

    public function testUpdateEmailToSame()
    {
        $client = $this->generateClient();
        $client->updateEmail($email = $this->generateEmail());
        $client->flushEvents();
        $updatedAt = $client->getUpdatedAt();

        $client->updateEmail($email);
        $this->assertSame($updatedAt, $client->getUpdatedAt());
        $this->assertEvents($client, []);
    }
    public function testUpdateEmailToNull()
    {
        $client = $this->generateClient();
        $client->updateEmail($this->generateEmail());
        $client->updateEmail(null);
        $this->assertNull($client->getContacts()->getEmail());
    }

    public function testUpdateEmailIfAnotherConfirmedEmailExists()
    {
        $client = $this->generateClient();
        $client->updateEmail($this->generateEmail());
        $client->confirmEmail();

        $client->updateEmail($email = $this->generateEmail());

        $this->assertEquals($email, $client->getContacts()->getEmail());
        $this->assertFalse($client->getContacts()->isEmailConfirmed());
    }

    public function testConfirmPhone()
    {
        $client = $this->generateClient();
        $updatedAt = $client->getUpdatedAt();

        $client->confirmPhone();
        $this->assertNotSame($updatedAt, $client->getUpdatedAt());
        $this->assertTrue($client->getContacts()->isPhoneConfirmed());
    }

    public function testConfirmPhoneIfPhoneAlreadyConfirmed()
    {
        $client = $this->generateClient();
        $client->confirmPhone();

        $this->expectException(\DomainException::class);
        $client->confirmPhone();
    }

    public function testConfirmEmail()
    {
        $client = $this->generateClient();
        $client->updateEmail($this->generateEmail());
        $updatedAt = $client->getUpdatedAt();

        $client->confirmEmail();
        $this->assertNotSame($updatedAt, $client->getUpdatedAt());
        $this->assertTrue($client->getContacts()->isEmailConfirmed());
    }

    public function testConfirmEmailIfClientDoesNotHaveEmail()
    {
        $this->expectException(\DomainException::class);
        $client = $this->generateClient();
        $client->confirmEmail();
    }

    public function testConfirmEmailIfAlreadyConfirmed()
    {
        $client = $this->generateClient();
        $client->updateEmail($this->generateEmail());
        $client->confirmEmail();

        $this->expectException(\DomainException::class);
        $client->confirmEmail();
    }
}
