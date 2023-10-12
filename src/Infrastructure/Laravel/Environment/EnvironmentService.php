<?php

namespace Project\Infrastructure\Laravel\Environment;

use Project\Common\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Project\Modules\Client\Api\ClientsApi;
use Project\Common\Environment\Client\Client;
use Project\Common\Repository\NotFoundException;
use Project\Common\Environment\EnvironmentInterface;

class EnvironmentService implements EnvironmentInterface
{
    public function __construct(
        private ClientsApi $clients,
        private string $hashCookieName = 'clientHash',
        private int $hashCookieLength = 40,
    ) {}

    public function getClient(): Client
    {
        return new Client(
            $this->retrieveClientHashCookie(),
            $this->retrieveClientId()
        );
    }

    private function retrieveClientHashCookie(): string
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

    private function retrieveClientId(): int
    {
        try {
            $client = $this->clients->get($this->retrieveClientHashCookie());
        } catch (NotFoundException) {
            $client = $this->clients->create($this->retrieveClientHashCookie());
        }
        return $client->id;
    }

    public function getLanguage(): Language
    {
        return Language::from(App::currentLocale());
    }
}