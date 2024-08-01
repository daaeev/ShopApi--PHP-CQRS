<?php

namespace Project\Common\Services\Environment;

interface EnvironmentInterface
{
    public function getClient(): Client;

    public function getAdministrator(): ?Administrator;

    public function getLanguage(): Language;
}