<?php

namespace Project\Common\ApplicationMessages\Events;

use Project\Common\Utils;

abstract class Event implements Utils\Arrayable
{
    public function toArray(): array
    {
        return [
            'data' => $this->getDTO()->toArray(),
            'className' => static::class
        ];
    }

    abstract public function getDTO(): Utils\DTO;
}