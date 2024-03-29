<?php

namespace Project\Modules\Client\Api\DTO;

use Project\Common\Utils\DTO;
use Project\Common\Utils\DateTimeFormat;

class Client implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $hash,
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
            'hash' => $this->hash,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phone' => $this->phone,
            'email' => $this->email,
            'phoneConfirmed' => $this->phoneConfirmed,
            'emailConfirmed' => $this->emailConfirmed,
            'createdAt' => $this->createdAt->format(DateTimeFormat::FULL_DATE->value),
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::FULL_DATE->value)
        ];
    }
}