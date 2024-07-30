<?php

namespace Project\Modules\Administrators\Api;

use Project\Modules\Administrators\Api\DTO\Admin;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;
use Project\Modules\Administrators\Utils\AdministratorEntity2DTOConverter;

class AdministratorsApi
{
    public function __construct(
        private readonly AuthManagerInterface $authManager
    ) {}

    public function getAuthenticated(): ?Admin
    {
        if (!$authenticated = $this->authManager->logged()) {
            return null;
        }

        return AdministratorEntity2DTOConverter::convert($authenticated);
    }
}