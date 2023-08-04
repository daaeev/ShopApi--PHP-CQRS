<?php

namespace Project\Common\CQRS\Buses\Interfaces;

interface BusInterface
{
    public function dispatch(object $command);

    public function canDispatch(object $command): bool;
}