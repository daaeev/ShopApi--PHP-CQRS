<?php

namespace Project\Modules\Administrators\Queries;

class GetAdminQuery
{
    public function __construct(
        public readonly int $id
    ) {}
}