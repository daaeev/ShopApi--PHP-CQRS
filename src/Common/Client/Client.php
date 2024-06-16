<?php

namespace Project\Common\Client;

use Webmozart\Assert\Assert;

class Client
{
    public function __construct(
        private string $hash,
        private ?int $id = null,
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
        $sameHash = $this->hash === $other->hash;
        $sameId = $this->id === $other->id;
        return !empty($this->id) ? $sameId || $sameHash : $sameHash;
    }
}