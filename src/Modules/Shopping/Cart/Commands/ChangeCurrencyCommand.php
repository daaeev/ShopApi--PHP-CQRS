<?php

namespace Project\Modules\Shopping\Cart\Commands;

class ChangeCurrencyCommand
{
    public function __construct(
        public readonly string $currency
    ) {}
}