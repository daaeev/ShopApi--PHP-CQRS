<?php

namespace Project\Modules\Administrators\Queries\Handlers;

use Project\Modules\Administrators\Queries\AdminsListQuery;
use Project\Modules\Administrators\Repository\QueryAdminsRepositoryInterface;

class AdminsListHandler
{
    public function __construct(
        private QueryAdminsRepositoryInterface $admins
    ) {}

    public function __invoke(AdminsListQuery $query): array
    {
        return $this->admins->list($query->page, $query->limit, $query->options)->toArray();
    }
}