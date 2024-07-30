<?php

namespace Project\Common\Entity\Id;

use Webmozart\Assert\Assert;

class StringId extends Id
{
    public function __construct(string $id)
    {
        Assert::notEmpty($id, 'String id cant be empty');
        parent::__construct($id);
    }

    public static function random(): static
    {
        return new static(uniqid());
    }
}