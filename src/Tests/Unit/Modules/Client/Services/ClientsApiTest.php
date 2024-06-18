<?php

namespace Project\Tests\Unit\Modules\Client\Services;

use PHPUnit\Framework\TestCase;
use Project\Modules\Client\Entity;
use Project\Modules\Client\Api\DTO;
use Project\Modules\Client\Api\ClientsApi;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Client\Repository\QueryClientsRepositoryInterface;

class ClientsApiTest extends TestCase
{
    use ContactsGenerator;

    private readonly ClientsRepositoryInterface $clients;
    private readonly QueryClientsRepositoryInterface $queryClients;
    private readonly MessageBusInterface $eventBus;
    private readonly ClientsApi $api;

    private readonly Hydrator $hydrator;
    private readonly int $clientId;
    private readonly DTO\Client $clientDTO;

    protected function setUp(): void
    {
        $this->clients = $this->getMockBuilder(ClientsRepositoryInterface::class)->getMock();
        $this->queryClients = $this->getMockBuilder(QueryClientsRepositoryInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $this->api = new ClientsApi($this->clients, $this->queryClients);
        $this->api->setDispatcher($this->eventBus);

        $this->hydrator = new Hydrator;
        $this->clientId = rand();
        $this->clientDTO = $this->getMockBuilder(DTO\Client::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetByPhone()
    {
        $this->queryClients->expects($this->once())
            ->method('getByPhone')
            ->with($phone = $this->generatePhone())
            ->willReturn($this->clientDTO);

        $client = $this->api->getByPhone($phone);
        $this->assertSame($client, $this->clientDTO);
    }

    public function testGetByPhoneIfDoesNotExists()
    {
        $this->queryClients->expects($this->once())
            ->method('getByPhone')
            ->willThrowException(new NotFoundException);

        $client = $this->api->getByPhone($this->generatePhone());
        $this->assertNull($client);
    }

    public function testCreate()
    {
        $firstName = uniqid();
        $lastName = uniqid();
        $phone = $this->generatePhone();
        $email = $this->generateEmail();

        $this->clients->expects($this->once())
            ->method('add')
            ->with($this->callback(function (Entity\Client $client) use ($firstName, $lastName, $phone, $email) {
                $this->assertSame($firstName, $client->getName()->getFirstName());
                $this->assertSame($lastName, $client->getName()->getLastName());
                $this->assertSame($phone, $client->getContacts()->getPhone());
                $this->assertSame($email, $client->getContacts()->getEmail());
                $this->hydrator->hydrate($client->getId(), ['id' => $this->clientId]);
                return true;
            }));

        $this->eventBus->expects($this->exactly(2))->method('dispatch');

        $client = $this->api->create($firstName, $lastName, $phone, $email);
        $this->assertSame($this->clientId, $client->id);
        $this->assertSame($firstName, $client->firstName);
        $this->assertSame($lastName, $client->lastName);
        $this->assertSame($phone, $client->phone);
        $this->assertSame($email, $client->email);
    }
}