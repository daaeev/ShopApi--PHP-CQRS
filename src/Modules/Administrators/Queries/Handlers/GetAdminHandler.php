<?php

namespace Project\Modules\Administrators\Queries\Handlers;

use Project\Modules\Administrators\Queries\GetAdminQuery;
use Project\Modules\Administrators\Repository\QueryAdminRepositoryInterface;

class GetAdminHandler
{
    public function __construct(
        private QueryAdminRepositoryInterface $admins
    ) {}

    public function __invoke(GetAdminQuery $query): array
    {
        return $this->admins->get($query->id)->toArray();
    }
}