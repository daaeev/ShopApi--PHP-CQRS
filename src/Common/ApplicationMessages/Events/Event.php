<?php

namespace Project\Common\ApplicationMessages\Events;

use Project\Common\Utils;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

abstract class Event implements Utils\Arrayable, ApplicationMessageInterface
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