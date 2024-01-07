<?php

namespace App\Http\Utils;

use Project\Common\Events\Event;
use Project\Common\CQRS\ApplicationMessagesManager;

trait DispatchRequests
{
    protected ApplicationMessagesManager $messagesManager;

    protected function dispatchCommand(object $command): mixed
    {
        return $this->messagesManager->dispatchCommand($command);
    }

    protected function dispatchQuery(object $query): mixed
    {
        return $this->messagesManager->dispatchQuery($query);
    }

    protected function dispatchEvent(Event $event): void
    {
        $this->messagesManager->dispatchEvent($event);
    }
}