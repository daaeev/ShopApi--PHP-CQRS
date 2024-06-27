<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CreatePromocodeCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly int $discountPercent,
        public readonly \DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
    ) {}
}