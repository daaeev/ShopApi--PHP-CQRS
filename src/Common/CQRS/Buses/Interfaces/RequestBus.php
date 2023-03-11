<?php

namespace Project\Common\CQRS\Buses\Interfaces;

interface RequestBus
{
    public function dispatch(object $command);

    public function canDispatch($command): bool;
}