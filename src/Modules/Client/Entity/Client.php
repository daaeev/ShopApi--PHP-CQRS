<?php

namespace Project\Modules\Client\Entity;

use Project\Common\Entity\Aggregate;
use Project\Modules\Client\Api\Events\ClientUpdated;
use Project\Modules\Client\Api\Events\ClientCreated;

class Client extends Aggregate
{
    private ClientId $id;
    private Name $name;
    private Contacts $contacts;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(ClientId $id, string $phone) {
        $this->id = $id;
        $this->name = new Name;
        $this->contacts = new Contacts($phone);
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

    public function updateEmail(?string $email): void
    {
        if ($this->contacts->getEmail() === $email) {
            return;
        }

        $this->contacts = $this->contacts->updateEmail($email);
        $this->updated();
    }

    public function confirmPhone(): void
    {
        $this->contacts = $this->contacts->confirmPhone();
        $this->updated();
    }

    public function confirmEmail(): void
    {
        $this->contacts = $this->contacts->confirmEmail();
        $this->updated();
    }

    public function getId(): ClientId
    {
        return $this->id;
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