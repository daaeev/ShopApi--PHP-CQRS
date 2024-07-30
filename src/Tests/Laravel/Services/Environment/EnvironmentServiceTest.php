<?php

namespace Project\Tests\Laravel\Services\Environment;

use Project\Common\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Project\Modules\Administrators\Api\DTO\Admin;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Administrators\Api\AdministratorsApi;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Project\Infrastructure\Laravel\Services\EnvironmentService;

class EnvironmentServiceTest extends \Project\Tests\Laravel\TestCase
{
    private readonly AdministratorsApi $administrators;
    private readonly SymfonyCookie $queuedCookie;
    private readonly EnvironmentInterface $environment;

    private readonly \Mockery\MockInterface $cookieMock;
    private readonly string $hashCookieName;
    private readonly string $hash;

    protected function setUp(): void
    {
        $this->administrators = $this->getMockBuilder(AdministratorsApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queuedCookie = $this->getMockBuilder(SymfonyCookie::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->hash = uniqid();
        $this->hashCookieName = uniqid();
        $this->environment = new EnvironmentService(
            $this->administrators,
            $this->hashCookieName,
            mb_strlen($this->hash)
        );

        parent::setUp();

        // Swap the actual Cookie facade with the mock.
        // Cookie::get() method mock always return null in default mock
        $this->cookieMock = \Mockery::mock('alias:' . Cookie::class);
        $this->app->instance(Cookie::class, $this->cookieMock);
    }

    public function testGetClient()
    {
        $this->cookieMock->shouldReceive('queued')
            ->with($this->hashCookieName)
            ->andReturnNull();

        $this->cookieMock->shouldReceive('get')
            ->with($this->hashCookieName)
            ->andReturn($this->hash);

        $client = $this->environment->getClient();
        $this->assertNull($client->getId());
        $this->assertSame($this->hash, $client->getHash());
    }

    public function testGetClientIfHashCookieQueued()
    {
        $this->queuedCookie->expects($this->once())
            ->method('getValue')
            ->willReturn($this->hash);

        $this->cookieMock->shouldReceive('queued')
            ->once()
            ->with($this->hashCookieName)
            ->andReturn($this->queuedCookie);

        $client = $this->environment->getClient();
        $this->assertNull($client->getId());
        $this->assertSame($this->hash, $client->getHash());
    }

    public function testGetClientIfHashCookieDoesNotExists()
    {
        $this->cookieMock->shouldReceive('queued')
            ->with($this->hashCookieName)
            ->andReturnNull();

        $this->cookieMock->shouldReceive('get')
            ->with($this->hashCookieName)
            ->andReturnNull();

        $this->expectException(\DomainException::class);
        $this->environment->getClient();
    }

    public function testGetClientIfHashLengthDoesNotEqualsHashCookieLength()
    {
        $this->cookieMock->shouldReceive('queued')
            ->with($this->hashCookieName)
            ->andReturnNull();

        $this->cookieMock->shouldReceive('get')
            ->with($this->hashCookieName)
            ->andReturn($this->hash . uniqid());

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