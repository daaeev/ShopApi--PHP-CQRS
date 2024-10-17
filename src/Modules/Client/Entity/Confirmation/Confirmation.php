<?php

namespace Project\Modules\Client\Entity\Confirmation;

class Confirmation
{
    private ConfirmationUuid $uuid;
    private int|string $code;
    private \DateTimeImmutable $expiredAt;

    public function __construct(
        ConfirmationUuid $uuid,
        int|string $code,
        int $lifeTimeInMinutes = 5,
    ) {
        $this->uuid = $uuid;
        $this->code = $code;
        $this->refreshExpiredAt($lifeTimeInMinutes);
    }

    public function refreshExpiredAt(int $lifeTimeInMinutes = 5): void
    {
        $this->expiredAt = new \DateTimeImmutable("+$lifeTimeInMinutes minutes");
    }

    public function validateCode(int|string $inputCode): void
    {
        if ($inputCode !== $this->code) {
            throw new \DomainException('Input code does not match');
        }

        $now = new \DateTimeImmutable;
        if ($now > $this->expiredAt) {
            throw new \DomainException('Input code expired');
        }
    }

    public function getUuid(): ConfirmationUuid
    {
        return $this->uuid;
    }

    public function getCode(): int|string
    {
        return $this->code;
    }

    public function getExpiredAt(): \DateTimeImmutable
    {
        return $this->expiredAt;
    }
}