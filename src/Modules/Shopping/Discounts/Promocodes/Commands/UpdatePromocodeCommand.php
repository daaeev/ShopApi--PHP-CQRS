<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Commands;

class UpdatePromocodeCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly \DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
    ) {}
}