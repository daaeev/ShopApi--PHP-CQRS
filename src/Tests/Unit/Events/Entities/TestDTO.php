<?php

namespace Project\Tests\Unit\Events\Entities;

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