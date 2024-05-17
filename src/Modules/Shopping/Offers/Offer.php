<?php

namespace Project\Modules\Shopping\Offers;

use Webmozart\Assert\Assert;

class Offer
{
    public function __construct(
        private OfferId $id,
        private OfferUuId $uuid,
        private int $product,
        private string $name,
        private float $regularPrice, // Price without any discounts
        private float $price, // Price with discounts
        private int $quantity,
        private ?string $size = null,
        private ?string $color = null,
    ) {
        $this->guardQuantityGreaterThanZero();
        $this->guardPriceGreaterThanZero();
        $this->guardReqularPriceGreaterThanPrice();
    }

    private function guardQuantityGreaterThanZero(): void
    {
        Assert::greaterThan($this->quantity, 0);
    }

    private function guardPriceGreaterThanZero(): void
    {
        Assert::greaterThanEq($this->regularPrice, 0);
        Assert::greaterThanEq($this->price, 0);
    }

    private function guardReqularPriceGreaterThanPrice()
    {
        Assert::greaterThanEq($this->regularPrice, $this->price);
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
        $this->uuid = clone $this->uuid;
    }

    public function equalsTo(self $other): bool
    {
        $sameId = $this->id->equalsTo($other->id) || $this->uuid->equalsTo($other->uuid);
        $sameOffer = ($this->getProduct() === $other->getProduct())
            && ($this->getRegularPrice() === $other->getRegularPrice())
            && ($this->getPrice() === $other->getPrice())
            && ($this->getSize() === $other->getSize())
            && ($this->getColor() === $other->getColor());

        return $sameId || $sameOffer;
    }

    public function getId(): OfferId
    {
        return $this->id;
    }

    public function getUuid(): OfferUuId
    {
        return $this->uuid;
    }

    public function getProduct(): int
    {
        return $this->product;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRegularPrice(): float
    {
        return $this->regularPrice;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
}