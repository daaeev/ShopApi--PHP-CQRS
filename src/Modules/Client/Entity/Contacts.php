<?php

namespace Project\Modules\Client\Entity;

class Contacts
{
    public function __construct(
        private ?string $phone = null,
        private ?string $email = null,
        private bool $phoneConfirmed = false,
        private bool $emailConfirmed = false,
    ) {
        if ($this->phoneConfirmed && empty($this->phone)) {
            throw new \DomainException('Empty phone cant be confirmed');
        }

        if ($this->emailConfirmed && empty($this->email)) {
            throw new \DomainException('Empty email cant be confirmed');
        }
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function isPhoneConfirmed(): bool
    {
        return $this->phoneConfirmed;
    }

    public function isEmailConfirmed(): bool
    {
        return $this->emailConfirmed;
    }

    public function equalsTo(self $other): bool
    {
        return (
            ($other->getPhone() === $this->getPhone())
            && ($other->getEmail() === $this->getEmail())
            && ($other->isPhoneConfirmed() === $this->isPhoneConfirmed())
            && ($other->isEmailConfirmed() === $this->isEmailConfirmed())
        );
    }
}