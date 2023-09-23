<?php

namespace Project\Modules\Administrators\Queries\Handlers;

use Project\Modules\Administrators\Queries\GetAdminQuery;
use Project\Modules\Administrators\Utils\AdministratorEntity2DTOConverter;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;

class AuthorizedAdminHandler
{
    public function __construct(
        private AuthManagerInterface $auth,
    ) {}

    public function __invoke(GetAdminQuery $query): array
    {
        if (!($admin = $this->auth->logged())) {
            throw new \DomainException('You are not logged in');
        }

        return AdministratorEntity2DTOConverter::convert($admin)->toArray();
    }
}