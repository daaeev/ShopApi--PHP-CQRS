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
            $entity->getName()->getFirstName(),
            $entity->getName()->getLastName(),
            $entity->getContacts()->getPhone(),
            $entity->getContacts()->getEmail(),
            $entity->getContacts()->isPhoneConfirmed(),
            $entity->getContacts()->isEmailConfirmed(),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }
}