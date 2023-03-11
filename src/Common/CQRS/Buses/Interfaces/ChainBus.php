<?php

namespace Project\Common\CQRS\Buses\Interfaces;

interface ChainBus
{
    public function dispatch(object $command);

    public function registerBus(RequestBus $bus): void;
}