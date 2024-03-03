<?php

namespace Project\Tests\Unit\Modules\Client\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Client\Api\Events\ClientUpdated;
use Project\Tests\Unit\Modules\Helpers\ClientFactory;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;

class ClientContactsTest extends \PHPUnit\Framework\TestCase
{
    use ClientFactory, ContactsGenerator, AssertEvents;

    public function testUpdatePhone()
    {
        $client = $this->generateClient();
        $client->updatePhone($phone = $this->generatePhone());
        $this->assertEquals($phone, $client->getContacts()->getPhone());
        $this->assertNotEmpty($client->getUpdatedAt());
        $this->assertEvents($client, [new ClientUpdated($client)]);
    }

    public function testUpdatePhoneToSame()
    {
        $client = $this->generateClient();
        $client->updatePhone($phone = $this->generatePhone());
        $client->flushEvents();
        $updatedAt = $client->getUpdatedAt();

        $client->updatePhone($phone);

        $this->assertSame($updatedAt, $client->getUpdatedAt());
        $this->assertEvents($client, []);
    }

    public function testUpdatePhoneToNull()
    {
        $client = $this->generateClient();
        $client->updatePhone($this->generatePhone());
        $client->updatePhone(null);
        $this->assertNull($client->getContacts()->getPhone());
    }

    public function testUpdatePhoneIfAnotherContactsExists()
    {
        $client = $this->generateClient();
        $client->updateEmail($email = $this->generateEmail());
        $client->confirmEmail();

        $client->updatePhone($phone = $this->generatePhone());

        $this->assertEquals($email, $client->getContacts()->getEmail());
        $this->assertTrue($client->getContacts()->isEmailConfirmed());
        $this->assertEquals($phone, $client->getContacts()->getPhone());
    }

    public function testUpdatePhoneIfAnotherConfirmedPhoneExists()
    {
        $client = $this->generateClient();
        $client->updatePhone($this->generatePhone());
        $client->confirmPhone();

        $client->updatePhone($phone = $this->generatePhone());

        $this->assertEquals($phone, $client->getContacts()->getPhone());
        $this->assertFalse($client->getContacts()->isPhoneConfirmed());
    }

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

    public function testUpdateEmailIfAnotherContactsExists()
    {
        $client = $this->generateClient();
        $client->updatePhone($phone = $this->generatePhone());
        $client->confirmPhone();
        $client->updateEmail($email = $this->generateEmail());

        $this->assertEquals($phone, $client->getContacts()->getPhone());
        $this->assertTrue($client->getContacts()->isPhoneConfirmed());
        $this->assertEquals($email, $client->getContacts()->getEmail());
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
        $client->updatePhone($this->generatePhone());
        $updatedAt = $client->getUpdatedAt();

        $client->confirmPhone();

        $this->assertNotSame($updatedAt, $client->getUpdatedAt());
        $this->assertTrue($client->getContacts()->isPhoneConfirmed());
    }

    public function testConfirmPhoneIfClientDoesNotHavePhone()
    {
        $this->expectException(\DomainException::class);
        $client = $this->generateClient();
        $client->confirmPhone();
    }

    public function testConfirmPhoneIfAlreadyConfirmed()
    {
        $client = $this->generateClient();
        $client->updatePhone($this->generatePhone());
        $client->confirmPhone();

        $this->expectException(\DomainException::class);
        $client->confirmPhone();
    }

    public function testConfirmPhoneIfClientHasAnotherContacts()
    {
        $client = $this->generateClient();
        $client->updateEmail($email = $this->generateEmail());
        $client->confirmEmail();
        $client->updatePhone($this->generatePhone());
        $updatedAt = $client->getUpdatedAt();

        $client->confirmPhone();

        $this->assertEquals($email, $client->getContacts()->getEmail());
        $this->assertTrue($client->getContacts()->isEmailConfirmed());
        $this->assertNotSame($updatedAt, $client->getUpdatedAt());
        $this->assertTrue($client->getContacts()->isPhoneConfirmed());
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

    public function testConfirmEmailIfClientHasAnotherContacts()
    {
        $client = $this->generateClient();
        $client->updatePhone($phone = $this->generatePhone());
        $client->confirmPhone();
        $client->updateEmail($this->generateEmail());
        $updatedAt = $client->getUpdatedAt();

        $client->confirmEmail();

        $this->assertEquals($phone, $client->getContacts()->getPhone());
        $this->assertTrue($client->getContacts()->isPhoneConfirmed());
        $this->assertNotSame($updatedAt, $client->getUpdatedAt());
        $this->assertTrue($client->getContacts()->isEmailConfirmed());
    }
}
