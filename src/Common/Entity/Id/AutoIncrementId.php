<?php

namespace Project\Common\Entity\Id;

class AutoIncrementId extends Id
{
    public function __construct(int $id = null)
    {
        parent::__construct($id);
    }

    public static function random(): static
    {
        return new static(random_int(1, 9999));
    }

    public static function next(): static
    {
        return new static(null);
    }
}