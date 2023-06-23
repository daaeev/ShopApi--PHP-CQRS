<?php

namespace Project\Common\Environment\Client;

use Webmozart\Assert\Assert;

class Client
{
    public function __construct(
        private $hash
    ) {
        Assert::notEmpty($hash, 'Client hash does not instantiated');
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}