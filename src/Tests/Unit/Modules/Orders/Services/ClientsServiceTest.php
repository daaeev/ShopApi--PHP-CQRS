<?php

namespace Project\Tests\Unit\Modules\Orders\Services;

use PHPUnit\Framework\TestCase;
use Project\Common\Client\Client;
use Project\Modules\Client\Api\DTO;
use Project\Modules\Client\Api\ClientsApi;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Adapters\ClientsService;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;

class ClientsServiceTest extends TestCase
{
    use ContactsGenerator;

    private readonly EnvironmentInterface $environment;
    private readonly ClientsApi $clients;
    private readonly ClientsService $service;

    protected function setUp(): void
    {
        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();
        $this->clients = $this->getMockBuilder(ClientsApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new ClientsService($this->environment, $this->clients);
    }

    public function testFindClientIfClientByPhoneDoesNotExists()
    {
        $environmentClient = new Client(uniqid());
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($environmentClient);

        $firstName = uniqid();
        $lastName = uniqid();
        $phone = $this->generatePhone();
        $email = $this->generateEmail();

        $this->clients->expects($this->once())
            ->method('getByPhone')
            ->with($phone)
            ->willReturn(null);

        $createdClient = $this->getClientDTO();
        $this->clients->expects($this->once())
            ->method('create')
            ->with($firstName, $lastName, $phone, $email)
            ->willReturn($createdClient);

        $foundedClient = $this->service->findClient($firstName, $lastName, $phone, $email);
        $this->assertSame($foundedClient->getHash(), $environmentClient->getHash());
        $this->assertSame($foundedClient->getId(), $createdClient->id);
    }

    private function getClientDTO(): DTO\Client
    {
        return new DTO\Client(
            id: rand(1, 10),
            firstName: uniqid(),
            lastName: uniqid(),
            phone: $this->generatePhone(),
            email: $this->generateEmail(),
            phoneConfirmed: true,
            emailConfirmed: true,
            createdAt: new \DateTimeImmutable,
            updatedAt: new \DateTimeImmutable,
        );
    }

    public function testFindClientIfClientAuthorized()
    {
        $environmentClient = new Client(hash: uniqid(), id: rand(1, 10));
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($environmentClient);

        $firstName = uniqid();
        $lastName = uniqid();
        $phone = $this->generatePhone();
        $email = $this->generateEmail();

        $foundedClient = $this->service->findClient($firstName, $lastName, $phone, $email);
        $this->assertSame($environmentClient, $foundedClient);
    }

    public function testFindClientIfClientByPhoneExists()
    {
        $environmentClient = new Client(uniqid());
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($environmentClient);

        $firstName = uniqid();
        $lastName = uniqid();
        $phone = $this->generatePhone();
        $email = $this->generateEmail();

        $clientByPhone = $this->getClientDTO();
        $this->clients->expects($this->once())
            ->method('getByPhone')
            ->with($phone)
            ->willReturn($clientByPhone);

        $foundedClient = $this->service->findClient($firstName, $lastName, $phone, $email);
        $this->assertSame($foundedClient->getHash(), $environmentClient->getHash());
        $this->assertSame($foundedClient->getId(), $clientByPhone->id);
    }
}