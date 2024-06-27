<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class DeletePromocodeCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
    ) {}
}