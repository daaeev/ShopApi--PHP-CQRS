<?php

namespace Project\Tests\Unit\Modules\Administrators\Services;

use Project\Modules\Administrators\Entity\Admin;
use Project\Tests\Unit\Modules\Helpers\AdminFactory;
use Project\Modules\Administrators\Api\AdministratorsApi;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;

class AdministratorsApiTest extends \PHPUnit\Framework\TestCase
{
    use AdminFactory;

    private readonly AuthManagerInterface $authManager;
    private readonly AdministratorsApi $api;

    private readonly Admin $admin;

    protected function setUp(): void
    {
        $this->authManager = $this->getMockBuilder(AuthManagerInterface::class)->getMock();
        $this->api = new AdministratorsApi($this->authManager);
        $this->admin = $this->generateAdmin();
    }

    public function testGetAuthenticated()
    {
        $this->authManager->expects($this->once())
            ->method('logged')
            ->willReturn($this->admin);

        $authenticated = $this->api->getAuthenticated();
        $this->assertNotNull($authenticated);
        $this->assertSame($authenticated->id, $this->admin->getId()->getId());
        $this->assertSame($authenticated->name, $this->admin->getName());
        $this->assertSame($authenticated->login, $this->admin->getLogin());
        $this->assertSame($authenticated->roles, $this->admin->getRoles());
    }

    public function testGetAuthenticatedIfAdminUnauthenticated()
    {
        $this->authManager->expects($this->once())
            ->method('logged')
            ->willReturn(null);

        $authenticated = $this->api->getAuthenticated();
        $this->assertNull($authenticated);
    }
}