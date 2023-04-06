<?php

namespace Project\Common\Entity\Id;

class IntegerId extends Id
{
    public function __construct(int $id = null)
    {
        parent::__construct($id);
    }

    public static function random(): Id
    {
        return new self(random_int(1, 9999));
    }

    public static function next(): Id
    {
        return new self(null);
    }
}