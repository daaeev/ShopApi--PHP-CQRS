<?php

namespace Project\Infrastructure\Laravel\ApplicationMessages;

use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\ApplicationMessagesManager;

trait DispatchMessagesTrait
{
    protected function dispatchCommand(object $command): mixed
    {
        return app()->make(ApplicationMessagesManager::class)->dispatchCommand($command);
    }

    protected function dispatchQuery(object $query): mixed
    {
        return app()->make(ApplicationMessagesManager::class)->dispatchQuery($query);
    }

    protected function dispatchEvent(Event $event): void
    {
        app()->make(ApplicationMessagesManager::class)->dispatchEvent($event);
    }
}