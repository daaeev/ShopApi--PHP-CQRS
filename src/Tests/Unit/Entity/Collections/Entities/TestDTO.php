<?php

namespace Project\Tests\Unit\Entity\Collections\Entities;

use Project\Common\Utils\DTO;

class TestDTO implements DTO
{
    public function __construct(
        private array $data
    ) {}

    public function toArray(): array
    {
        return $this->data;
    }
}