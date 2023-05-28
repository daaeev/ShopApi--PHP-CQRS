<?php

namespace App\Http\Utils;

trait DispatchRequests
{
    protected function dispatchCommand(object $command): mixed
    {
        return app()->make('CommandBus')->dispatch($command);
    }

    protected function dispatchQuery(object $query): mixed
    {
        return app()->make('QueryBus')->dispatch($query);
    }

    protected function dispatchEvent(object $event): mixed
    {
        return app()->make('EventBus')->dispatch($event);
    }
}