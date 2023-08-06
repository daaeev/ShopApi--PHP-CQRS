<?php

namespace Project\Infrastructure\Laravel\Environment;

use Project\Common\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Project\Common\Environment\Client\Client;
use Project\Common\Environment\EnvironmentInterface;

class EnvironmentService implements EnvironmentInterface
{
    public function __construct(
        private string $hashCookieName = 'clientHash'
    ) {}

    public function getClient(): Client
    {
        $hash = Request::cookie($this->hashCookieName);
        return new Client($hash);
    }

    public function getLanguage(): Language
    {
        return Language::from(App::currentLocale());
    }
}