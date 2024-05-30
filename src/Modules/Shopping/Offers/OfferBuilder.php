<?php

namespace Project\Modules\Shopping\Offers;

class OfferBuilder
{
    private ?OfferId $id = null;
    private ?OfferUuId $uuid = null;
    private ?int $product = null;
    private ?string $name = null;
    private ?int $regularPrice = null; // Price without any discounts
    private ?int $price = null; // Price with discounts
    private ?int $quantity = null;
    private ?string $size = null;
    private ?string $color = null;

    public function from(Offer $offer): self
    {
        $builder = new self;
        $builder->id = clone $offer->getId();
        $builder->uuid = clone $offer->getUuid();
        $builder->product = $offer->getProduct();
        $builder->name = $offer->getName();
        $builder->regularPrice = $offer->getRegularPrice();
        $builder->price = $offer->getPrice();
        $builder->quantity = $offer->getQuantity();
        $builder->size = $offer->getSize();
        $builder->color = $offer->getColor();
        return $builder;
    }

    public function withNullableId(): self
    {
        $this->id = OfferId::next();
        return $this;
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

    public function build(): Offer
    {
        $offer = new Offer(
            $this->id,
            $this->uuid,
            $this->product,
            $this->name,
            $this->regularPrice,
            $this->price,
            $this->quantity,
            $this->size,
            $this->color,
        );

        $this->id = null;
        $this->uuid = null;
        $this->product = null;
        $this->name = null;
        $this->regularPrice = null;
        $this->price = null;
        $this->quantity = null;
        $this->size = null;
        $this->color = null;

        return $offer;
    }
}