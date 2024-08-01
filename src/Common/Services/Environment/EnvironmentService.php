<?php

namespace Project\Common\Services\Environment;

use Illuminate\Support\Facades\App;
use Project\Modules\Administrators\Api\AdministratorsApi;
use Project\Common\Services\Cookie\CookieManagerInterface;

class EnvironmentService implements EnvironmentInterface
{
    public function __construct(
        private CookieManagerInterface $cookie,
        private AdministratorsApi $administrators,
        private string $hashCookieName = 'clientHash',
    ) {}

    public function getClient(): Client
    {
        return new Client($this->getClientHashCookie(), $this->getAuthorizedClientId());
    }

    private function getClientHashCookie(): string
    {
        if (empty($hash = $this->cookie->get($this->hashCookieName))) {
            throw new \DomainException('Client hash cookie does not instantiated');
        }

        return $hash;
    }

    private function getAuthorizedClientId(): ?int
    {
        return null; // TODO: Not implemented yet
    }

    public function getAdministrator(): ?Administrator
    {
        if (empty($authenticated = $this->administrators->getAuthenticated())) {
            return null;
        }

        return new Administrator($authenticated->id, $authenticated->name);
    }

    public function getLanguage(): Language
    {
        return Language::from(App::currentLocale());
    }
}