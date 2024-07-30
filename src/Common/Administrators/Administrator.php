<?php

namespace Project\Common\Administrators;

use Webmozart\Assert\Assert;

class Administrator
{
    public function __construct(
        private int $id,
        private string $name,
    ) {
        Assert::notEmpty($id);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function same(self $other): bool
    {
        return $this->id === $other->id;
    }
}