<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity;

use Webmozart\Assert\Assert;
use Project\Common\Entity\Duration;
use Project\Common\Entity\Aggregate;
use Project\Modules\Shopping\Api\Events\Promotions as Events;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\AbstractDiscountMechanic;

class Promotion extends Aggregate
{
    private PromotionId $id;
    private string $name;
    private Duration $duration;
    private bool $disabled;
    private array $discounts;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        PromotionId $id,
        string $name,
        Duration $duration,
        bool $disabled = false,
        array $discounts = []
    ) {
        Assert::allIsInstanceOf($discounts, AbstractDiscountMechanic::class);

        $this->id = $id;
        $this->name = $name;
        $this->duration = $duration;
        $this->disabled = $disabled;
        $this->discounts = $discounts;
        $this->createdAt = new \DateTimeImmutable;

        $this->addEvent(new Events\PromotionCreated($this));
    }

	public function __clone(): void
	{
		$this->id = clone $this->id;
		$this->duration = clone $this->duration;
		$this->createdAt = clone $this->createdAt;
		$this->updatedAt = $this->updatedAt ? clone $this->updatedAt : null;
	}

	public function disable(): void
    {
        if ($this->disabled) {
            throw new \DomainException('Promotion already disabled');
        }

        $this->disabled = true;
        $this->updated();
    }

    private function updated(): void
    {
        $this->updatedAt = new \DateTimeImmutable;
        $this->addEvent(new Events\PromotionUpdated($this));
    }

    public function enable(): void
    {
        if (!$this->disabled) {
            throw new \DomainException('Promotion already enabled');
        }

        $this->disabled = false;
        $this->updated();
    }

    public function updateDuration(Duration $duration): void
    {
        $this->guardPromotionNotActive();

        if ($duration->equalsTo($this->duration)) {
            return;
        }

        $this->duration = $duration;
        $this->updated();
    }

    private function guardPromotionNotActive(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Cant update/delete active promotion');
        }
    }

    private function isActive(): bool
    {
        return !$this->disabled && $this->duration->started();
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

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function disabled(): bool
    {
        return $this->disabled;
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