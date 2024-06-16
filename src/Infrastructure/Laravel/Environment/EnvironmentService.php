<?php

namespace Project\Infrastructure\Laravel\Environment;

use Project\Common\Language;
use Project\Common\Client\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Project\Common\Environment\EnvironmentInterface;

class EnvironmentService implements EnvironmentInterface
{
    public function __construct(
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

    public function getLanguage(): Language
    {
        return Language::from(App::currentLocale());
    }
}