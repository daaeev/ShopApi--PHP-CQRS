<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity;

use Webmozart\Assert\Assert;
use Project\Common\Events\EventRoot;
use Project\Common\Events\EventTrait;
use Project\Modules\Shopping\Api\Events\Promotions as Events;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;

class Promotion implements EventRoot
{
    use EventTrait;

    private PromotionId $id;
    private string $name;
    private \DateTimeImmutable $startDate;
    private ?\DateTimeImmutable $endDate;
    private array $discounts;
    private bool $disabled = false;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        PromotionId $id,
        string $name,
        \DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate = null,
        array $discounts = []
    ) {
        Assert::allIsInstanceOf($discounts, AbstractDiscountMechanic::class);

        $this->id = $id;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->discounts = $discounts;
        $this->createdAt = new \DateTimeImmutable;

        $this->guardEndDateGreaterThanStartDate();
        $this->addEvent(new Events\PromotionCreated($this));
    }

    private function guardEndDateGreaterThanStartDate(): void
    {
        if (empty($this->endDate)) {
            return;
        }

        Assert::greaterThan(
            $this->endDate,
            $this->startDate,
            'End date must be greater than start date'
        );
    }

    public function disable(): void
    {
        if ($this->disabled()) {
            throw new \DomainException('Promotion already disabled');
        }

        $this->disabled = true;
        $this->updated();
    }

    public function disabled(): bool
    {
        return $this->disabled;
    }

    private function updated(): void
    {
        $this->updatedAt = new \DateTimeImmutable;
        $this->addEvent(new Events\PromotionUpdated($this));
    }

    public function enable(): void
    {
        if (!$this->disabled()) {
            throw new \DomainException('Promotion already enabled');
        }

        $this->disabled = false;
        $this->updated();
    }

    public function isActive(): bool
    {
        return !$this->disabled && $this->started() && !$this->ended();
    }

    public function started(): bool
    {
        $now = new \DateTimeImmutable;
        return $now > $this->startDate;
    }

    public function ended(): bool
    {
        $now = new \DateTimeImmutable;
        return !empty($this->endDate) && ($now > $this->endDate);
    }

    public function getActualStatus(): PromotionStatus
    {
        if ($this->disabled) {
            return PromotionStatus::DISABLED;
        }

        if (!$this->started()) {
            return PromotionStatus::NOT_STARTED;
        }

        if ($this->ended()) {
            return PromotionStatus::ENDED;
        }

        return PromotionStatus::STARTED;
    }

    public function updateStartDate(\DateTimeImmutable $startDate): void
    {
        $this->guardPromotionNotActive();

        if ($startDate == $this->startDate) {
            return;
        }

        $this->startDate = $startDate;
        $this->guardEndDateGreaterThanStartDate();
        $this->updated();
    }

    private function guardPromotionNotActive(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Cant update/delete active promotion');
        }
    }

    public function updateEndDate(?\DateTimeImmutable $endDate): void
    {
        $this->guardPromotionNotActive();

        if ($endDate == $this->endDate) {
            return;
        }

        $this->endDate = $endDate;
        $this->guardEndDateGreaterThanStartDate();
        $this->updated();
    }

    public function addDiscount(AbstractDiscountMechanic $discount): void
    {
        $this->guardPromotionNotActive();

        if ($this->discountExists($discount->getId())) {
            throw new \DomainException('Promotion already has discount with same id');
        }

        $this->discounts[] = $discount;
        $this->updated();
    }

    private function discountExists(DiscountMechanicId $discountId): bool
    {
        foreach ($this->discounts as $discount) {
            if ($discountId->equalsTo($discount->getId())) {
                return true;
            }
        }

        return false;
    }

    public function removeDiscount(DiscountMechanicId $discountId): void
    {
        $this->guardPromotionNotActive();

        if (!$this->discountExists($discountId)) {
            throw new \DomainException('Promotion does not have discount with id ' . $discountId->getId());
        }

        foreach ($this->discounts as $index => $discount) {
            if ($discountId->equalsTo($discount->getId())) {
                unset($this->discounts[$index]);
                break;
            }
        }

        $this->updated();
    }

    public function delete()
    {
        $this->guardPromotionNotActive();
        $this->addEvent(new Events\PromotionDeleted($this));
    }

    public function updateName(string $name): void
    {
        $this->guardPromotionNotActive();

        if ($name === $this->name) {
            return;
        }

        $this->name = $name;
        $this->updated();
    }

    public function getId(): PromotionId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * @return AbstractDiscountMechanic[]
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
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