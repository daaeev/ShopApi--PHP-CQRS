<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Events\EventRoot;

trait AssertEventsTrait
{
    private function assertEvents(EventRoot $entity, array $events): void
    {
        $entityEvents = $entity->flushEvents();
        $this->assertCount(count($events), $entityEvents);

        foreach ($events as $event) {
            $this->assertTrue(in_array($event, $entityEvents));
        }
    }
}