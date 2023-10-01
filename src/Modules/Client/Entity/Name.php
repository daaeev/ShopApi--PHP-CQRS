<?php

namespace Project\Modules\Client\Entity;

class Name
{
    public function __construct(
        private ?string $firstName = null,
        private ?string $lastName = null,
    ) {
        if (empty($this->firstName) && !empty($this->lastName)) {
            throw new \DomainException('Client name cannot consist only of the last name');
        }
    }

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
        if (empty($this->firstName) && empty($this->lastName)) {
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