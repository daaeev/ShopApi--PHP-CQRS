<?php

namespace Project\Modules\Shopping\Cart\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UsePromocodeCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $promocode
    ) {}
}