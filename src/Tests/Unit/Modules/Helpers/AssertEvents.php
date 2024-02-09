<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Entity\Aggregate;

trait AssertEvents
{
    private function assertEvents(Aggregate $entity, array $events): void
    {
        $entityEvents = $entity->flushEvents();
        $this->assertCount(
            count($events),
            $entityEvents,
            'Expect ' . count($events) . ' entity events. ' . count($entityEvents) . ' provided'
        );

        foreach ($events as $event) {
            $eventExists = in_array($event, $entityEvents);
            $this->assertTrue(
                $eventExists,
                'Entity does not have ' . $event::class . ' event'
            );
        }
    }
}