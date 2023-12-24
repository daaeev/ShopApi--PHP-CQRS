<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

use Project\Common\Utils\Arrayable;

abstract class AbstractDiscountMechanic implements Arrayable
{
    public function __construct(
        protected DiscountMechanicId $id,
        protected DiscountType $type,
        protected array $data = [],
    ) {}

    public function getId(): DiscountMechanicId
    {
        return $this->id;
    }

    public function getType(): DiscountType
    {
        return $this->type;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getId(),
            'type' => $this->type->value,
            'data' => $this->data
        ];
    }
}