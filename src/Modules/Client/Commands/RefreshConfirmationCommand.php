<?php

namespace Project\Modules\Client\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class RefreshConfirmationCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $confirmationUuid,
    ) {}
}