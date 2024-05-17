<?php

namespace Project\Common\Entity\Id;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid extends Id
{
    public function __construct(UuidInterface $uuid)
    {
        parent::__construct($uuid->toString());
    }

    public static function random(): static
    {
        return new static(RamseyUuid::uuid4());
    }
}