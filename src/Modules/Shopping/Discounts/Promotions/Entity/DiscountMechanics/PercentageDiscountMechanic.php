<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

use Webmozart\Assert\Assert;

class PercentageDiscountMechanic extends AbstractDiscountMechanic
{
    public function __construct(DiscountMechanicId $id, array $data = [])
    {
        Assert::keyExists($data, 'percent', 'Percentage discount mechanic need percent argument');
        Assert::numeric($data['percent'], 'Discount percent must be integer');
        Assert::greaterThanEq($data['percent'], 0, 'Discount percent must be greater or equal than 0');
        Assert::lessThanEq($data['percent'], 100, 'Discount percent must be less or equal than 100');

        parent::__construct($id, DiscountType::PERCENTAGE, $data);
    }

    public function getPercent(): int
    {
        return $this->data['percent'];
    }
}