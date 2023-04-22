<?php

namespace Project\Common\Events;

interface EventRoot
{
    public function flushEvents(): array;

    public function addEvent(Event $event): void;
}