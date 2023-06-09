<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Events\EventRoot;

trait AssertEvents
{
    private function assertEvents(EventRoot $entity, array $events): void
    {
        $entityEvents = $entity->flushEvents();
        $this->assertCount(count($events), $entityEvents, 'Expect ' . count($events) . ' entity events. ' . count($entityEvents) . ' provided');

        foreach ($events as $event) {
            $this->assertTrue(in_array($event, $entityEvents), 'Entity does not have ' . $event::class . ' event');
        }
    }
}