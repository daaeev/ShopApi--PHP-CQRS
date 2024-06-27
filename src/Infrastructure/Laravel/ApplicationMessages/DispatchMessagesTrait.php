<?php

namespace Project\Infrastructure\Laravel\ApplicationMessages;

use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\ApplicationMessagesManager;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

trait DispatchMessagesTrait
{
    protected function dispatchCommand(ApplicationMessageInterface $command): mixed
    {
        return app()->make(ApplicationMessagesManager::class)->dispatchCommand($command);
    }

    protected function dispatchQuery(ApplicationMessageInterface $query): mixed
    {
        return app()->make(ApplicationMessagesManager::class)->dispatchQuery($query);
    }

    protected function dispatchEvent(Event $event): void
    {
        app()->make(ApplicationMessagesManager::class)->dispatchEvent($event);
    }
}