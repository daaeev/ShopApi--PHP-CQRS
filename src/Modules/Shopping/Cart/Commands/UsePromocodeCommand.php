<?php

namespace Project\Modules\Shopping\Cart\Commands;

class UsePromocodeCommand
{
    public function __construct(
        public readonly string $promocode
    ) {}
}