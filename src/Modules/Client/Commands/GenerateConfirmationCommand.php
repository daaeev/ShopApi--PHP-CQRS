<?php

namespace Project\Modules\Client\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GenerateConfirmationCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $phone
    ) {}
}