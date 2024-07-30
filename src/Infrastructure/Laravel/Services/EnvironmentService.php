<?php

namespace Project\Infrastructure\Laravel\Services;

use Project\Common\Language;
use Project\Common\Client\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Project\Common\Administrators\Administrator;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Administrators\Api\AdministratorsApi;

class EnvironmentService implements EnvironmentInterface
{
    public function __construct(
        private AdministratorsApi $administrators,
        private string $hashCookieName = 'clientHash',
        private int $hashCookieLength = 40,
    ) {}

    public function getClient(): Client
    {
        return new Client(
            $this->getClientHashCookie(),
            $this->getAuthorizedClientId()
        );
    }

    private function getClientHashCookie(): string
    {
        $queued = Cookie::queued($this->hashCookieName);
        $hash = $queued ? $queued->getValue() : Cookie::get($this->hashCookieName);
        if (empty($hash)) {
            throw new \DomainException('Client hash cookie does not instantiated');
        }

        if (mb_strlen($hash) !== $this->hashCookieLength) {
            throw new \DomainException('Client hash cookie does not decrypted. Make sure that EncryptCookies middleware is enabled');
        }

        return $hash;
    }

    private function getAuthorizedClientId(): ?int
    {
        return null; // TODO: Not implemented yet
    }

    public function getAdministrator(): ?Administrator
    {
        $authenticated = $this->administrators->getAuthenticated();
        if (null === $authenticated) {
            return null;
        }

        return new Administrator($authenticated->id, $authenticated->name);
    }

    public function getLanguage(): Language
    {
        return Language::from(App::currentLocale());
    }
}