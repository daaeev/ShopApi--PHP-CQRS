<?php

namespace Project\Modules\Cart\Entity;

use Webmozart\Assert\Assert;

class CartItem
{
    public function __construct(
        private CartItemId $id,
        private int $product,
        private string $name,
        private float $price,
        private int $quantity,
        private ?string $size = null,
        private ?string $color = null,
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

    public function equalsTo(self $other): bool
    {
        return (
            ($this->getProduct() === $other->getProduct())
            && ($this->getName() === $other->getName())
            && ($this->getPrice() === $other->getPrice())
            && ($this->getSize() === $other->getSize())
            && ($this->getColor() === $other->getColor())
        );
    }

    public function getId(): CartItemId
    {
        return $this->id;
    }

    public function getProduct(): int
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

    public function getColor(): ?string
    {
        return $this->color;
    }
}