<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CreatePromotionCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $name,
        public readonly ?\DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
        public readonly bool $disabled,
        public readonly array $discounts = [],
    ) {}
}