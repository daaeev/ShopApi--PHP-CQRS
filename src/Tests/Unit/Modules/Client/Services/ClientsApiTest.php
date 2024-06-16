<?php

namespace Project\Tests\Unit\Modules\Client\Services;

use PHPUnit\Framework\TestCase;
use Project\Modules\Client\Entity;
use Project\Modules\Client\Api\DTO;
use Project\Modules\Client\Api\ClientsApi;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;
use Project\Modules\Client\Repository\ClientsRepositoryInterface;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Client\Repository\QueryClientsRepositoryInterface;

class ClientsApiTest extends TestCase
{
    use ContactsGenerator;

    private readonly ClientsRepositoryInterface $clientsRepository;
    private readonly QueryClientsRepositoryInterface $queryClientsRepository;
    private readonly MessageBusInterface $eventBus;
    private readonly ClientsApi $api;

    private readonly Hydrator $hydrator;
    private readonly DTO\Client $clientDTO;

    protected function setUp(): void
    {
        $this->clientsRepository = $this->getMockBuilder(ClientsRepositoryInterface::class)->getMock();
        $this->queryClientsRepository = $this->getMockBuilder(QueryClientsRepositoryInterface::class)->getMock();
        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $this->api = new ClientsApi($this->clientsRepository, $this->queryClientsRepository);
        $this->api->setDispatcher($this->eventBus);

        $this->hydrator = new Hydrator;
        $this->clientDTO = $this->getMockBuilder(DTO\Client::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGet()
    {
        $id = rand(1, 10);
        $this->queryClientsRepository->expects($this->once())
            ->method('get')
            ->with($id)
            ->willReturn($this->clientDTO);

        $client = $this->api->get($id);
        $this->assertSame($this->clientDTO, $client);
    }

    public function testCreate()
    {
        $firstName = uniqid();
        $lastName = uniqid();
        $phone = $this->generatePhone();
        $email = $this->generateEmail();

        $this->clientsRepository->expects($this->once())
            ->method('add')
            ->with($this->callback(function (Entity\Client $client) use ($firstName, $lastName, $phone, $email) {
                $this->assertSame($client->getName()->getFirstName(), $firstName);
                $this->assertSame($client->getName()->getLastName(), $lastName);
                $this->assertSame($client->getContacts()->getPhone(), $phone);
                $this->assertSame($client->getContacts()->getEmail(), $email);

                $this->hydrator->hydrate($client->getId(), ['id' => rand(1, 10)]);
                return true;
            }));

        $this->eventBus->expects($this->exactly(2))->method('dispatch');

        $client = $this->api->create($firstName, $lastName, $phone, $email);
        $this->assertSame($client->firstName, $firstName);
        $this->assertSame($client->lastName, $lastName);
        $this->assertSame($client->phone, $phone);
        $this->assertSame($client->email, $email);
    }
}