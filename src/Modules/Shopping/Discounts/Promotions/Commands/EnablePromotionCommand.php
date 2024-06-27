<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class EnablePromotionCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
    ) {}
}