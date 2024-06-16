<?php

namespace Project\Modules\Client\Api\DTO;

use Project\Common\Utils\DTO;

class Client implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly bool $phoneConfirmed,
        public readonly bool $emailConfirmed,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phone' => $this->phone,
            'email' => $this->email,
            'phoneConfirmed' => $this->phoneConfirmed,
            'emailConfirmed' => $this->emailConfirmed,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::RFC3339),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::RFC3339)
        ];
    }
}