<?php

namespace Project\Common\Client;

use Webmozart\Assert\Assert;

class Client
{
    public function __construct(
        private string $hash,
        private int $id,
    ) {
        Assert::notEmpty($hash, 'Client hash does not instantiated');
        Assert::notEmpty($id, 'Client id cant be empty');
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getId(): int
    {
        return $this->id;
    }
}