<?php

namespace Project\Tests\Unit\Modules\Client\Repository;

use Project\Modules\Client\Entity\Name;
use Project\Modules\Client\Entity\ClientId;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\ClientFactory;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;

trait ClientsRepositoryTestTrait
{
    use ClientFactory, ContactsGenerator;

    protected ClientsRepositoryInterface $clients;

    public function testAdd()
    {
        $initial = $this->generateClient();
        $phone = $initial->getContacts()->getPhone();
        $initial->confirmPhone();
        $initial->updateEmail($email = $this->generateEmail());
        $initial->confirmEmail();
        $initial->updateName($name = new Name('FirstName', 'LastName'));
        $this->clients->add($initial);

        $found = $this->clients->get($initial->getId());
        $this->assertSame($initial, $found);
        $this->assertTrue($found->getId()->equalsTo($initial->getId()));
        $this->assertTrue($found->getName()->equalsTo($name));
        $this->assertSame($found->getContacts()->getPhone(), $phone);
        $this->assertTrue($found->getContacts()->isPhoneConfirmed());
        $this->assertSame($found->getContacts()->getEmail(), $email);
        $this->assertTrue($found->getContacts()->isEmailConfirmed());
        $this->assertSame($found->getCreatedAt()->getTimestamp(), $initial->getCreatedAt()->getTimestamp());
        $this->assertSame($found->getUpdatedAt()?->getTimestamp(), $initial->getUpdatedAt()?->getTimestamp());
    }

    public function testAddIncrementIds()
    {
        $client = $this->makeClient(ClientId::next(), $this->generatePhone());
        $this->clients->add($client);
        $this->assertNotNull($client->getId()->getId());
    }

    public function testAddWithDuplicatedId()
    {
        $client = $this->generateClient();
        $clientWithSameId = $this->makeClient($client->getId(), $this->generatePhone());
        $this->clients->add($client);
        $this->expectException(DuplicateKeyException::class);
        $this->clients->add($clientWithSameId);
    }

    public function testAddWithNotUniquePhone()
    {
        $client = $this->generateClient();
        $this->clients->add($client);

        $clientWithNotUniquePhone = $this->makeClient(ClientId::next(), $client->getContacts()->getPhone());
        $this->expectException(DuplicateKeyException::class);
        $this->clients->add($clientWithNotUniquePhone);
    }

    public function testAddWithNotUniqueEmail()
    {
        $client = $this->generateClient();
        $client->updateEmail($this->generateEmail());
        $this->clients->add($client);

        $clientWithNotUniqueEmail = $this->generateClient();
        $clientWithNotUniqueEmail->updateEmail($client->getContacts()->getEmail());

        $this->expectNotToPerformAssertions();
        $this->clients->add($clientWithNotUniqueEmail);
    }

    public function testUpdate()
    {
        $initial = $this->generateClient();
        $this->clients->add($initial);

        $added = $this->clients->get($initial->getId());
        $phone = $initial->getContacts()->getPhone();
        $added->confirmPhone();
        $added->updateEmail($email = $this->generateEmail());
        $added->confirmEmail();
        $added->updateName($name = new Name('FirstNameUpdated', 'LastNameUpdated'));
        $createdAt = $added->getCreatedAt();
        $updatedAt = $added->getUpdatedAt();
        $this->clients->update($added);

        $updated = $this->clients->get($initial->getId());
        $this->assertSame($initial, $added);
        $this->assertSame($added, $updated);
        $this->assertTrue($updated->getName()->equalsTo($name));
        $this->assertSame($updated->getContacts()->getPhone(), $phone);
        $this->assertTrue($updated->getContacts()->isPhoneConfirmed());
        $this->assertSame($updated->getContacts()->getEmail(), $email);
        $this->assertTrue($updated->getContacts()->isEmailConfirmed());
        $this->assertSame($updated->getCreatedAt()->getTimestamp(), $createdAt->getTimestamp());
        $this->assertSame($updated->getUpdatedAt()->getTimestamp(), $updatedAt->getTimestamp());
    }

    public function testUpdateIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $client = $this->generateClient();
        $this->clients->update($client);
    }

    public function testUpdateSameClientAndDoesNotChangeAnything()
    {
        $client = $this->generateClient();
        $this->clients->add($client);
        $this->clients->update($client);
        $this->expectNotToPerformAssertions();
    }

    public function testDelete()
    {
        $client = $this->generateClient();
        $this->clients->add($client);
        $this->clients->delete($client);
        $this->expectException(NotFoundException::class);
        $this->clients->get($client->getId());
    }

    public function testDeleteIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $client = $this->generateClient();
        $this->clients->delete($client);
    }

    public function testGet()
    {
        $initial = $this->generateClient();
        $this->clients->add($initial);

        $founded = $this->clients->get($initial->getId());
        $this->assertSame($initial, $founded);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->clients->get(ClientId::random());
    }
}
