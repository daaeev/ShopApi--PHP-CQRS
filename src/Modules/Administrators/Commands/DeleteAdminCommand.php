<?php

namespace Project\Modules\Administrators\Commands;

class DeleteAdminCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}