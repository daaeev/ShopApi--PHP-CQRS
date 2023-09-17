<?php

namespace Project\Modules\Client\Utils;

use Project\Modules\Client\Entity;
use Project\Modules\Client\Api\DTO;

class ClientEntity2DTOConverter
{
    public static function convert(Entity\Client $entity): DTO\Client
    {
        return new DTO\Client(
            $entity->getId()->getId(),
            $entity->getHash()->getId(),
            $entity->getFirstName(),
            $entity->getLastName(),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }
}