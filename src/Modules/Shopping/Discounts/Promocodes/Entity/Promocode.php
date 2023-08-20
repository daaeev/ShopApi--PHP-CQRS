<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Entity;

use Project\Common\Events;
use Webmozart\Assert\Assert;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeCreated;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeUpdated;
use Project\Modules\Shopping\Api\Events\Promocodes\PromocodeDeleted;

class Promocode implements Events\EventRoot
{
    use Events\EventTrait;

    private bool $active;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        private PromocodeId $id,
        private string $name,
        private string $code,
        private int $discountPercent,
        private \DateTimeImmutable $startDate,
        private ?\DateTimeImmutable $endDate,
    ) {
        $this->active = true;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = null;
        $this->guardCodeDoesNotEmpty();
        $this->guardDiscountLessThanOneHundred();
        $this->guardValidActiveDates();
        $this->addEvent(new PromocodeCreated($this));
    }

    private function guardCodeDoesNotEmpty(): void
    {
        Assert::notEmpty($this->code, 'Promo-code cant be an empty string');
    }

    private function guardDiscountLessThanOneHundred(): void
    {
        Assert::lessThanEq(
            $this->discountPercent,
            100,
            'Promo-code discount percent must be less or equal than 100'
        );
    }

    private function guardValidActiveDates(): void
    {
        if (empty($this->endDate)) {
            return;
        }

        if ($this->endDate < $this->startDate) {
            throw new \DomainException('End date must be greater than start date');
        }
    }

    public function isActive(): bool
    {
        $currentTime = new \DateTimeImmutable();

        if ($currentTime < $this->startDate) {
            return false;
        }

        if (!empty($this->endDate) && ($currentTime > $this->endDate)) {
            return false;
        }

        return $this->active === true;
    }

    public function setName(string $name): void
    {
        if ($name === $this->name) {
            return;
        }

        $this->name = $name;
        $this->updated();
    }

    private function updated(): void
    {
        $this->addEvent(new PromocodeUpdated($this));
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setCode(string $code): void
    {
        if ($code === $this->code) {
            return;
        }

        $this->code = $code;
        $this->guardCodeDoesNotEmpty();
        $this->updated();
    }

    public function setDiscountPercent(int $percent): void
    {
        if ($percent === $this->discountPercent) {
            return;
        }

        $this->discountPercent = $percent;
        $this->guardDiscountLessThanOneHundred();
        $this->updated();
    }

    public function setStartDate(\DateTimeImmutable $date): void
    {
        if ($date == $this->startDate) {
            return;
        }

        $this->startDate = $date;
        $this->guardValidActiveDates();
        $this->updated();
    }

    public function setEndDate(?\DateTimeImmutable $date): void
    {
        if ($date == $this->endDate) {
            return;
        }

        $this->endDate = $date;
        $this->guardValidActiveDates();
        $this->updated();
    }

    public function delete(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Cant delete active promo-code');
        }

        $this->addEvent(new PromocodeDeleted($this));
    }

    public function getId(): PromocodeId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDiscountPercent(): int
    {
        return $this->discountPercent;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}