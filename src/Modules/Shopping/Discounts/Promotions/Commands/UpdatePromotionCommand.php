<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

class UpdatePromotionCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?\DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
    ) {}
}