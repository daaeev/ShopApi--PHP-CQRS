<?php

namespace Project\Tests\Unit\Modules\Client\Repository;

use Project\Modules\Client\Entity\Name;
use Project\Common\Utils\DateTimeFormat;
use Project\Modules\Client\Entity\Client;
use Project\Modules\Client\Entity\ClientId;
use Project\Modules\Client\Entity\ClientHash;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\ClientFactory;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;

trait ClientRepositoryTestTrait
{
    use ClientFactory, ContactsGenerator;

    protected ClientsRepositoryInterface $clients;

    public function testAdd()
    {
        $initial = $this->generateClient();
        $initial->updatePhone($this->generatePhone());
        $initial->confirmPhone();
        $initial->updateEmail($this->generateEmail());
        $initial->confirmEmail();
        $initial->updateName(new Name('FirstName', 'LastName'));
        $this->clients->add($initial);
        $found = $this->clients->get($initial->getId());
        $this->assertSameClients($initial, $found);
    }

    private function assertSameClients(Client $initial, Client $other): void
    {
        $this->assertTrue($initial->getId()->equalsTo($other->getId()));
        $this->assertTrue($initial->getHash()->equalsTo($other->getHash()));
        $this->assertTrue($initial->getName()->equalsTo($other->getName()));
        $this->assertTrue($initial->getContacts()->equalsTo($other->getContacts()));
        $this->assertSame(
            $initial->getCreatedAt()->format(DateTimeFormat::FULL_DATE->value),
            $other->getCreatedAt()->format(DateTimeFormat::FULL_DATE->value)
        );
        $this->assertSame(
            $initial->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE->value),
            $other->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE->value)
        );
    }

    public function testAddIncrementIds()
    {
        $client = $this->makeClient(
            ClientId::next(),
            ClientHash::random()
        );
        $this->clients->add($client);
        $this->assertNotNull($client->getId()->getId());
    }

    public function testAddWithDuplicatedId()
    {
        $client = $this->generateClient();
        $clientWithSameId = $this->makeClient(
            $client->getId(),
            ClientHash::random()
        );
        $this->clients->add($client);
        $this->expectException(DuplicateKeyException::class);
        $this->clients->add($clientWithSameId);
    }

    public function testAddWithNotUniqueHash()
    {
        $client = $this->generateClient();
        $clientWithNotUniqueHash = $this->makeClient(
            ClientId::next(),
            $client->getHash()
        );
        $this->clients->add($client);
        $this->expectException(DuplicateKeyException::class);
        $this->clients->add($clientWithNotUniqueHash);
    }

    public function testAddWithNotUniquePhone()
    {
        $client = $this->generateClient();
        $clientWithNotUniquePhone = $this->generateClient();
        $client->updatePhone($this->generatePhone());
        $clientWithNotUniquePhone->updatePhone($client->getContacts()->getPhone());
        $this->clients->add($client);
        $this->expectException(DuplicateKeyException::class);
        $this->clients->add($clientWithNotUniquePhone);
    }

    public function testAddWithNotUniqueEmail()
    {
        $client = $this->generateClient();
        $clientWithNotUniqueEmail = $this->generateClient();
        $client->updateEmail($this->generateEmail());
        $clientWithNotUniqueEmail->updateEmail($client->getContacts()->getEmail());
        $this->clients->add($client);
        $this->expectException(DuplicateKeyException::class);
        $this->clients->add($clientWithNotUniqueEmail);
    }

    public function testUpdate()
    {
        $initial = $this->generateClient();
        $initial->updatePhone($this->generatePhone());
        $initial->confirmPhone();
        $initial->updateEmail($this->generateEmail());
        $initial->confirmEmail();
        $initial->updateName(new Name(
            'FirstNameInitial',
            'LastNameInitial'
        ));
        $this->clients->add($initial);
        $added = $this->clients->get($initial->getId());
        $added->updatePhone($this->generatePhone());
        $added->confirmPhone();
        $added->updateEmail($this->generateEmail());
        $added->confirmEmail();
        $added->updateName(new Name(
            'FirstNameUpdated',
            'LastNameUpdated'
        ));
        $this->clients->update($added);
        $updated = $this->clients->get($initial->getId());
        $this->assertSameClients($added, $updated);
        $this->assertFalse($initial->getName()->equalsTo($updated->getName()));
        $this->assertFalse($initial->getContacts()->equalsTo($updated->getContacts()));
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

    public function testUpdateWithNotUniquePhone()
    {
        $client = $this->generateClient();
        $clientWithNotUniquePhone = $this->generateClient();
        $client->updatePhone($this->generatePhone());
        $this->clients->add($client);
        $this->clients->add($clientWithNotUniquePhone);
        $clientWithNotUniquePhone->updatePhone($client->getContacts()->getPhone());
        $this->expectException(DuplicateKeyException::class);
        $this->clients->update($clientWithNotUniquePhone);
    }

    public function testUpdateWithNotUniqueEmail()
    {
        $client = $this->generateClient();
        $clientWithNotUniqueEmail = $this->generateClient();
        $client->updateEmail($this->generateEmail());
        $this->clients->add($client);
        $this->clients->add($clientWithNotUniqueEmail);
        $clientWithNotUniqueEmail->updateEmail($client->getContacts()->getEmail());
        $this->expectException(DuplicateKeyException::class);
        $this->clients->update($clientWithNotUniqueEmail);
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
        $foundedById = $this->clients->get($initial->getId());
        $foundedByHash = $this->clients->get($initial->getHash());
        $this->assertSameClients($initial, $foundedById);
        $this->assertSameClients($initial, $foundedByHash);
        $this->assertSameClients($foundedById, $foundedByHash);
    }

    public function testGetByIdIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->clients->get(ClientId::random());
    }

    public function testGetByHashIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->clients->get(ClientHash::random());
    }
}