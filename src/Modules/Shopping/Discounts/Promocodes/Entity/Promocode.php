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

    private PromocodeId $id;
    private string $name;
    private string $code;
    private int $discountPercent;
    private bool $active;
    private \DateTimeImmutable $startDate;
    private ?\DateTimeImmutable $endDate;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        PromocodeId $id,
        string $name,
        string $code,
        int $discountPercent,
        \DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->discountPercent = $discountPercent;
        $this->active = true;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->createdAt = new \DateTimeImmutable;
        $this->updatedAt = null;

        $this->guardNameDoesNotEmpty();
        $this->guardCodeDoesNotEmpty();
        $this->guardDiscountLessThanOneHundred();
        $this->guardDiscountGreaterThanZero();
        $this->guardEndDateGreaterThanStartDate();
        $this->addEvent(new PromocodeCreated($this));
    }

    private function guardNameDoesNotEmpty(): void
    {
        Assert::notEmpty($this->name, 'Promo-code name cant be an empty string');
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

    private function guardDiscountGreaterThanZero(): void
    {
        Assert::greaterThan(
            $this->discountPercent,
            0,
            'Promo-code discount percent must be greater than 0'
        );
    }

    private function guardEndDateGreaterThanStartDate(): void
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
        $this->guardPromocodeNotActive();

        if ($name === $this->name) {
            return;
        }

        $this->name = $name;
        $this->guardNameDoesNotEmpty();
        $this->updated();
    }

    private function guardPromocodeNotActive(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Cant update/delete active promo-code');
        }
    }

    private function updated(): void
    {
        $this->addEvent(new PromocodeUpdated($this));
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setStartDate(\DateTimeImmutable $date): void
    {
        $this->guardPromocodeNotActive();

        if ($date == $this->startDate) {
            return;
        }

        $this->startDate = $date;
        $this->guardEndDateGreaterThanStartDate();
        $this->updated();
    }

    public function setEndDate(?\DateTimeImmutable $date): void
    {
        $this->guardPromocodeNotActive();

        if ($date == $this->endDate) {
            return;
        }

        $this->endDate = $date;
        $this->guardEndDateGreaterThanStartDate();
        $this->updated();
    }

    public function activate(): void
    {
        if (true === $this->active) {
            throw new \DomainException('Promo-code already activated');
        }

        $this->active = true;
        $this->updated();
    }

    public function deactivate(): void
    {
        if (false === $this->active) {
            throw new \DomainException('Promo-code already deactivated');
        }

        $this->active = false;
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

    public function getActive(): bool
    {
        return $this->active;
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