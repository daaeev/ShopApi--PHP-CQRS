<?php

namespace Project\Tests\Unit\Events;

use Project\Tests\Unit\Events\Entities\TestEvent;
use Project\Tests\Unit\Events\Helpers\EventsFactory;

class EventTest extends \PHPUnit\Framework\TestCase
{
    use EventsFactory;

    public function testGetDto()
    {
        $dto = $this->makeDTO();
        $event = new TestEvent($dto);
        $eventDTO = $event->getDTO();
        $this->assertSame($dto, $eventDTO);
    }

    public function testDtoReturnConstructArray()
    {
        $arrayToCheck = [1, 2, 3, 4, 5];
        $dto = $this->makeDTO($arrayToCheck);
        $this->assertSame($arrayToCheck, $dto->toArray());
        $this->assertNotSame([1, 'Test'], $dto->toArray());
    }

    public function testEventToArrayStructure()
    {
        $dtoData = [1, 2, 3, 4, 5];
        $eventStructure = [
            'data' => $dtoData,
            'className' => TestEvent::class,

        ];
        $event = $this->makeEvent($dtoData);
        $this->assertSame($eventStructure, $event->toArray());
    }
}