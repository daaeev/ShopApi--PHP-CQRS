<?php

namespace Project\Modules\Client\Entity;

use Webmozart\Assert\Assert;
use Project\Common\Utils\ContactsValidator;

class Contacts
{
    public function __construct(
        private string $phone,
        private ?string $email = null,
        private bool $phoneConfirmed = false,
        private bool $emailConfirmed = false,
    ) {
        Assert::notEmpty($this->phone);
        ContactsValidator::validatePhone($this->phone);
        ContactsValidator::validateEmail($this->email);

        if ($this->emailConfirmed && empty($this->email)) {
            throw new \DomainException('Empty email cant be confirmed');
        }
    }

    public function updateEmail(?string $email): self
    {
        ContactsValidator::validateEmail($this->email);
        return new self(
            phone: $this->phone,
            email: $email,
            phoneConfirmed: $this->phoneConfirmed,
            emailConfirmed: false,
        );
    }

    public function confirmPhone(): self
    {
        if ($this->phoneConfirmed) {
            throw new \DomainException('Phone already confirmed');
        }

        return new Contacts(
            phone: $this->phone,
            email: $this->email,
            phoneConfirmed: true,
            emailConfirmed: $this->emailConfirmed,
        );
    }

    public function confirmEmail(): self
    {
        if (empty($this->email)) {
            throw new \DomainException('Client does not have email to confirm');
        }

        if ($this->emailConfirmed) {
            throw new \DomainException('Email already confirmed');
        }

        return new Contacts(
            phone: $this->phone,
            email: $this->email,
            phoneConfirmed: $this->phoneConfirmed,
            emailConfirmed: true,
        );
    }

    public function getPhone(): string
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