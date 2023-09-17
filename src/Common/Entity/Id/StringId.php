<?php

namespace Project\Common\Entity\Id;

class StringId extends Id
{
    public function __construct(string $id = null)
    {
        parent::__construct($id);
    }

    public static function random(): StringId
    {
        return new static(md5(rand()));
    }
}