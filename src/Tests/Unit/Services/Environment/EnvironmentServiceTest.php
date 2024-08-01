<?php

namespace Project\Tests\Unit\Services\Environment;

use Illuminate\Support\Facades\App;
use Project\Common\Services\Environment\Language;
use Project\Modules\Administrators\Api\DTO\Admin;
use Project\Modules\Administrators\Api\AdministratorsApi;
use Project\Common\Services\Cookie\CookieManagerInterface;
use Project\Common\Services\Environment\EnvironmentService;
use Project\Common\Services\Environment\EnvironmentInterface;

class EnvironmentServiceTest extends \PHPUnit\Framework\TestCase
{
    private readonly CookieManagerInterface $cookie;
    private readonly AdministratorsApi $administrators;
    private readonly EnvironmentInterface $environment;

    private readonly string $hashCookieName;
    private readonly string $hash;

    protected function setUp(): void
    {
        $this->cookie = $this->getMockBuilder(CookieManagerInterface::class)->getMock();
        $this->administrators = $this->getMockBuilder(AdministratorsApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->hashCookieName = uniqid();
        $this->hash = uniqid();
        $this->environment = new EnvironmentService(
            $this->cookie,
            $this->administrators,
            $this->hashCookieName
        );
    }

    public function testGetClient()
    {
        $this->cookie->expects($this->once())
            ->method('get')
            ->with($this->hashCookieName)
            ->willReturn($this->hash);

        $client = $this->environment->getClient();
        $this->assertNull($client->getId());
        $this->assertSame($this->hash, $client->getHash());
    }

    public function testGetClientIfHashCookieDoesNotExists()
    {
        $this->cookie->expects($this->once())
            ->method('get')
            ->with($this->hashCookieName)
            ->willReturn(null);

        $this->expectException(\DomainException::class);
        $this->environment->getClient();
    }

    public function testGetAdministrator()
    {
        $authenticated = new Admin(
            id: random_int(1, 9999),
            name: uniqid(),
            login: uniqid(),
            roles: []
        );

        $this->administrators->expects($this->once())
            ->method('getAuthenticated')
            ->willReturn($authenticated);

        $administrator = $this->environment->getAdministrator();
        $this->assertNotNull($administrator);
        $this->assertSame($administrator->getId(), $authenticated->id);
        $this->assertSame($administrator->getName(), $authenticated->name);
    }

    public function testGetAdministratorIfUnauthenticated()
    {
        $this->administrators->expects($this->once())
            ->method('getAuthenticated')
            ->willReturn(null);

        $administrator = $this->environment->getAdministrator();
        $this->assertNull($administrator);
    }

    public function testGetLanguage()
    {
        App::shouldReceive('currentLocale')
            ->once()
            ->andReturn(Language::default()->value);

        $language = $this->environment->getLanguage();
        $this->assertSame(Language::default(), $language);
    }
}