<?php

namespace Project\Modules\Client\Api\Events;

use Project\Modules\Client\Entity\Client;
use Project\Modules\Client\Entity\Confirmation\Confirmation;

abstract class AbstractClientConfirmationEvent extends AbstractClientEvent
{
    private int|string $code;
    private \DateTimeImmutable $expiredAt;

    public function __construct(Client $client, Confirmation $confirmation)
    {
        parent::__construct($client);
        $this->code = $confirmation->getCode();
        $this->expiredAt = $confirmation->getExpiredAt();
    }

    public function getData(): array
    {
        return [
            'client' => parent::getData(),
            'confirmation' => [
                'code' => $this->code,
                'expiredAt' => $this->expiredAt->format(\DateTimeInterface::RFC3339),
            ],
        ];
    }
}