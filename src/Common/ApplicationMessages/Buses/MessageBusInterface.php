<?php

namespace Project\Common\ApplicationMessages\Buses;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

interface MessageBusInterface
{
    public function dispatch(ApplicationMessageInterface $message);

    public function canDispatch(ApplicationMessageInterface $message): bool;
}