<?php

namespace Project\Tests\Unit\Events\Entities;

use Project\Common\Utils;
use Project\Common\ApplicationMessages\Events\Event;

class TestEvent extends Event
{
    public function __construct(
        private Utils\DTO $dto
    ) {}

    public function getDTO(): Utils\DTO
    {
        return $this->dto;
    }
}