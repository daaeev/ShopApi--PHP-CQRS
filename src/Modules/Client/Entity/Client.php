<?php

namespace Project\Modules\Client\Entity;

use Project\Common\Events;
use Project\Modules\Client\Api\Events\ClientUpdated;
use Project\Modules\Client\Api\Events\ClientCreated;

class Client implements Events\EventRoot
{
    use Events\EventTrait;

    private Name $name;
    private Contacts $contacts;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        private ClientId $id,
        private ClientHash $hash,
    ) {
        $this->name = new Name;
        $this->contacts = new Contacts;
        $this->createdAt = new \DateTimeImmutable;
        $this->addEvent(new ClientCreated($this));
    }

    public function updateName(Name $name): void
    {
        if ($this->name->equalsTo($name)) {
            return;
        }

        $this->name = $name;
        $this->updated();
    }

    private function updated(): void
    {
        $this->updatedAt = new \DateTimeImmutable;
        $this->addEvent(new ClientUpdated($this));
    }

    public function updatePhone(?string $phone): void
    {
        if ($this->contacts->getPhone() === $phone) {
            return;
        }

        $this->contacts = new Contacts(
            $phone,
            $this->contacts->getEmail(),
            false,
            $this->contacts->isEmailConfirmed(),
        );
        $this->updated();
    }

    public function updateEmail(?string $email): void
    {
        if ($this->contacts->getEmail() === $email) {
            return;
        }

        $this->contacts = new Contacts(
            $this->contacts->getPhone(),
            $email,
            $this->contacts->isPhoneConfirmed(),
            false,
        );
        $this->updated();
    }

    public function confirmPhone(): void
    {
        if (!$this->contacts->getPhone()) {
            throw new \DomainException('Client does not have phone number');
        }

        if ($this->contacts->isPhoneConfirmed()) {
            throw new \DomainException('Client phone already confirmed');
        }

        $this->contacts = new Contacts(
            $this->contacts->getPhone(),
            $this->contacts->getEmail(),
            true,
            $this->contacts->isEmailConfirmed(),
        );
        $this->updated();
    }

    public function confirmEmail(): void
    {
        if (!$this->contacts->getEmail()) {
            throw new \DomainException('Client does not have email');
        }

        if ($this->contacts->isEmailConfirmed()) {
            throw new \DomainException('Client email already confirmed');
        }

        $this->contacts = new Contacts(
            $this->contacts->getPhone(),
            $this->contacts->getEmail(),
            $this->contacts->isPhoneConfirmed(),
            true
        );
        $this->updated();
    }

    public function getId(): ClientId
    {
        return $this->id;
    }

    public function getHash(): ClientHash
    {
        return $this->hash;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getContacts(): Contacts
    {
        return $this->contacts;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}