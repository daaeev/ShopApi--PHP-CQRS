<?php

namespace Project\Tests\Unit\Entity\Hydrator;

class TestObject
{
    public function __construct(
        private int|string $initPrivateValue
    ) {}

    public function getInitPrivateValue(): int|string
    {
        return $this->initPrivateValue;
    }
}