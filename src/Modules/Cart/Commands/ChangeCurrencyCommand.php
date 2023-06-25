<?php

namespace Project\Modules\Cart\Commands;

class ChangeCurrencyCommand
{
    public function __construct(
        public readonly string $currency
    ) {}
}