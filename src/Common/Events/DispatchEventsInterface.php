<?php

namespace Project\Common\Events;

use Psr\EventDispatcher\EventDispatcherInterface;

interface DispatchEventsInterface
{
    public function setDispatcher(EventDispatcherInterface $dispatcher): void;

    public function dispatch(Event $event): void;
}