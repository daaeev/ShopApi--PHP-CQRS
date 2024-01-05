<?php

namespace Project\Common\CQRS\Buses;

interface MessageBusInterface
{
    public function dispatch(object $request);

    public function canDispatch(object $request): bool;
}