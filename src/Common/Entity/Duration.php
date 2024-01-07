<?php

namespace Project\Common\Entity;

use Webmozart\Assert\Assert;
use Project\Common\Utils\Arrayable;

class Duration implements Arrayable
{
    public function __construct(
        private ?\DateTimeImmutable $startDate = null,
        private ?\DateTimeImmutable $endDate = null,
    ) {
        Assert::notEmpty(
            $startDate || $endDate,
            'Duration start date or end date required'
        );

        Assert::nullOrGreaterThan(
            $endDate,
            $startDate,
            'End date must be greater than start date'
        );
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function notStarted(): bool
    {
        $now = new \DateTimeImmutable;
        return !empty($this->startDate) && ($now < $this->startDate);
    }

    public function started(): bool
    {
        return !$this->notStarted() && !$this->ended();
    }

    public function ended(): bool
    {
        $now = new \DateTimeImmutable;
        return !empty($this->endDate) && ($now > $this->endDate);
    }

    public function equalsTo(self $other): bool
    {
        return (
            ($this->startDate?->getTimestamp() === $other->startDate?->getTimestamp())
            && ($this->endDate?->getTimestamp() === $other->endDate?->getTimestamp())
        );
    }

    public function toArray(): array
    {
        return [
            'startDate' => $this->startDate?->format(\DateTimeInterface::RFC3339),
            'endDate' => $this->endDate?->format(\DateTimeInterface::RFC3339),
        ];
    }
}