<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UpdatePromocodeCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly \DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
    ) {}
}