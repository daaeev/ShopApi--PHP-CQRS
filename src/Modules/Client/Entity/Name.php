<?php

namespace Project\Modules\Client\Entity;

class Name
{
    public function __construct(
        private ?string $firstName = null,
        private ?string $lastName = null,
    ) {}

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName(): ?string
    {
        if (empty($this->firstName && $this->lastName)) {
            return null;
        }

        return $this->firstName . ($this->lastName ? ' ' . $this->lastName : '');
    }

    public function equalsTo(self $other): bool
    {
        return (
            ($other->getFirstName() === $this->getFirstName())
            && ($other->getLastName() === $this->getLastName())
        );
    }
}