<?php

namespace Project\Modules\Shopping\Cart\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class ChangeCurrencyCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $currency
    ) {}
}