<?php

namespace Project\Common\ApplicationMessages\Events;

use Project\Common\Utils;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

abstract class Event implements Utils\Arrayable, ApplicationMessageInterface
{
    final public function toArray(): array
    {
        return [
            'id' => $this->getEventId(),
            'data' => $this->getData(),
        ];
    }

    abstract public function getEventId(): string;

    abstract public function getData(): array;
}