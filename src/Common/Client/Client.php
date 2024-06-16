<?php

namespace Project\Common\Client;

use Webmozart\Assert\Assert;

class Client
{
    public function __construct(
        private string $hash,
        private ?int $id,
    ) {
        Assert::notEmpty($hash, 'Client hash required');
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function same(self $other): bool
    {
        if (!empty($this->id)) {
            return $this->id === $other->id;
        }

        return $this->hash === $other->hash;
    }
}