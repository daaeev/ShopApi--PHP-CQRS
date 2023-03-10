<?php

namespace Project\Common\CQRS\Buses\Interfaces;

interface RequestBus
{
    public function dispatch(object $command): mixed;

    public function canDispatch($command): bool;
}