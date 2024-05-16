<?php

namespace Project\Modules\Shopping\Entity;

use Webmozart\Assert\Assert;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\Promocode as BasePromocode;

class Promocode
{
    public function __construct(
        private PromocodeId $id,
        private string $code,
        private int $discountPercent,
    ) {
        Assert::notEmpty($this->code);
        Assert::greaterThanEq($this->discountPercent, 0);
        Assert::lessThanEq($this->discountPercent, 100);
    }

    public static function fromBaseEntity(BasePromocode $promocode): self
    {
        return new self(
            clone $promocode->getId(),
            $promocode->getCode(),
            $promocode->getDiscountPercent()
        );
    }

    public function getId(): PromocodeId
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDiscountPercent(): int
    {
        return $this->discountPercent;
    }
}