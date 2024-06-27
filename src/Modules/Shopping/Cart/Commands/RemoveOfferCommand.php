<?php

namespace Project\Modules\Shopping\Cart\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class RemoveOfferCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $item,
    ) {}
}