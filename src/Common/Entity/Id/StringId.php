<?php

namespace Project\Common\Entity\Id;

class StringId extends Id
{
    public function __construct(string $id)
    {
        if (mb_strlen($id) === 0) {
            throw new \DomainException('String id cant be empty');
        }

        parent::__construct($id);
    }

    public static function random(): static
    {
        return new static(md5(rand()));
    }
}