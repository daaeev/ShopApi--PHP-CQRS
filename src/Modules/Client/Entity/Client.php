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

    public function updateContacts(Contacts $contacts): void
    {
        if ($this->contacts->equalsTo($contacts)) {
            return;
        }

        $this->contacts = $contacts;
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