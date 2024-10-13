<?php

namespace Project\Modules\Client\Entity\Access;

abstract class Access
{
    public function equalsTo(self $other): bool
    {
        return ($other->getType() === $this->getType())
            && ($other->getCredentials() === $this->getCredentials());
    }

    abstract public function getType(): AccessType;

    abstract public function getCredentials(): array;
}