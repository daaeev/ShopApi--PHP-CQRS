<?php

namespace Project\Tests\Unit\Events\Entities;

use Project\Common\ApplicationMessages\Events\Event;

class TestEvent extends Event
{
    public function getEventId(): string
    {
        return 'test';
    }

    public function getData(): array
    {
        return ['test' => true];
    }
}