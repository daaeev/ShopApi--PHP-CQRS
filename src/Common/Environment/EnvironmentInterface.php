<?php

namespace Project\Common\Environment;

use Project\Common\Language;
use Project\Common\Client\Client;
use Project\Common\Administrators\Administrator;

interface EnvironmentInterface
{
    public function getClient(): Client;

    public function getAdministrator(): ?Administrator;

    public function getLanguage(): Language;
}