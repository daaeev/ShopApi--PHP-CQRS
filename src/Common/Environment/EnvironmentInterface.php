<?php

namespace Project\Common\Environment;

use Project\Common\Language;

interface EnvironmentInterface
{
    public function getClient(): Client\Client;

    public function getLanguage(): Language;
}