<?php

namespace Project\Tests\Unit\Entity\Collections\Mock;

class Stringable
{
    public function __toString(): string
    {
        return 'Stringable';
    }
}