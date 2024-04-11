<?php

namespace Project\Common\Environment;

use Project\Common\Language;
use Project\Common\Client\Client;

interface EnvironmentInterface
{
    public function getClient(): Client;

    public function getLanguage(): Language;
}