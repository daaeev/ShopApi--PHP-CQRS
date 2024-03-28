<?php

namespace Project\Modules\Shopping\Cart\Entity;

class CartItemBuilder
{
    private ?CartItemId $id = null;
    private ?int $product = null;
    private ?string $name = null;
    private ?float $regularPrice = null; // Price without any discounts
    private ?float $price = null; // Price with discounts
    private ?int $quantity = null;
    private ?string $size = null;
    private ?string $color = null;

    public function from(CartItem $item): self
    {
        $builder = new self;
        $builder->id = clone $item->getId();
        $builder->product = $item->getProduct();
        $builder->name = $item->getName();
        $builder->regularPrice = $item->getRegularPrice();
        $builder->price = $item->getPrice();
        $builder->quantity = $item->getQuantity();
        $builder->size = $item->getSize();
        $builder->color = $item->getColor();
        return $builder;
    }

    public function withPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function withQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function build(): CartItem
    {
        $cartItem = new CartItem(
            $this->id,
            $this->product,
            $this->name,
            $this->regularPrice,
            $this->price,
            $this->quantity,
            $this->size,
            $this->color,
        );

        $this->id = null;
        $this->product = null;
        $this->name = null;
        $this->regularPrice = null;
        $this->price = null;
        $this->quantity = null;
        $this->size = null;
        $this->color = null;

        return $cartItem;
    }
}