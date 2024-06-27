<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class ActivatePromocodeCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
    ) {}
}