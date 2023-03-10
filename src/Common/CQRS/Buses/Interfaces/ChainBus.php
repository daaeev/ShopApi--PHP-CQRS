<?php

namespace Project\Common\CQRS\Buses\Interfaces;

interface ChainBus
{
    public function dispatch($command): mixed;

    public function registerBus(RequestBus $bus): void;
}