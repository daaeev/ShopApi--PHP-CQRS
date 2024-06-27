<?php

namespace Project\Modules\Shopping\Order\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UpdateOfferCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $offerId,
        public readonly string $quantity,
    ) {}
}