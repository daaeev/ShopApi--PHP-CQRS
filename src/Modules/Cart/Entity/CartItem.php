<?php

namespace Project\Modules\Cart\Entity;

use Webmozart\Assert\Assert;

class CartItem
{
    public function __construct(
        private int|string $product,
        private string $name,
        private float $price,
        private int $quantity,
        private ?string $size = null,
        private ?ItemColor $color = null,
    ) {
        $this->guardQuantityGreaterThanZero();
        $this->guardPriceGreaterThanZero();
    }

    private function guardQuantityGreaterThanZero(): void
    {
        Assert::greaterThan($this->quantity, 0);
    }

    private function guardPriceGreaterThanZero(): void
    {
        Assert::greaterThan($this->price, 0);
    }

    public function updateQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->guardQuantityGreaterThanZero();
    }

    public function updatePrice(float $price): void
    {
        $this->price = $price;
        $this->guardPriceGreaterThanZero();
    }

    public function equalsTo(self $other): bool
    {
        return (
            ($this->getProduct() === $other->getProduct())
            && ($this->getName() === $other->getName())
            && ($this->getPrice() === $other->getPrice())
            && ($this->getQuantity() === $other->getQuantity())
            && ($this->getSize() === $other->getSize())
            && ($this->getColor()?->getColor() === $other->getColor()?->getColor())
        );
    }

    public function getProduct(): int|string
    {
        return $this->product;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getColor(): ?ItemColor
    {
        return $this->color;
    }
}