<?php

namespace Project\Modules\Administrators\Utils;

use Project\Modules\Administrators\Entity;
use Project\Modules\Administrators\Api\DTO;

class Entity2DTOConverter
{
    public static function convert(Entity\Admin $entity): DTO\Admin
    {
        return new DTO\Admin(
            $entity->getId()->getId(),
            $entity->getName(),
            $entity->getLogin(),
            $entity->getRoles(),
        );
    }
}