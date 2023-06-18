<?php

namespace Project\Common\Environment;

interface EnvironmentInterface
{
    public function getClient(): Client\Client;
}