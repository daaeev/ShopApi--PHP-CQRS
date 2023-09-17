<?php

namespace Project\Modules\Client\Entity;

class Contacts
{
    public function __construct(
        private ?string $phone = null,
        private ?string $email = null,
    ) {}

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function equalsTo(self $other): bool
    {
        return (
            ($other->getPhone() === $this->getPhone())
            && ($other->getEmail() === $this->getEmail())
        );
    }
}