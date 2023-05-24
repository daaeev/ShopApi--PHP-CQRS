<?php

namespace Project\Tests\Unit\Events\Helpers;

use Project\Common\Utils\DTO;
use Project\Common\Events\Event;
use Project\Tests\Unit\Events\Entities\TestDTO;
use Project\Tests\Unit\Events\Entities\TestEvent;

trait EventsFactory
{
    private function makeDTO(array $dtoData = []): DTO
    {
        return new TestDTO($dtoData);
    }

    private function makeEvent(array $eventData = []): Event
    {
        return new TestEvent(new TestDTO($eventData));
    }
}