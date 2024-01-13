<?php

namespace Project\Common\ApplicationMessages\Buses;

interface MessageBusInterface
{
    public function dispatch(object $request);

    public function canDispatch(object $request): bool;
}