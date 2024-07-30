<?php

namespace Project\Common\Entity\Id;

use Webmozart\Assert\Assert;

class IntegerId extends Id
{
    public function __construct(int $id)
    {
        Assert::greaterThan($id, 0, 'Integer id must be greater than 0');
        parent::__construct($id);
    }

    public static function random(): static
    {
        return new static(random_int(1, 9999));
    }
}