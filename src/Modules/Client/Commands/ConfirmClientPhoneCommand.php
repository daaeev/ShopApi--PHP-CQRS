<?php

namespace Project\Modules\Client\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class ConfirmClientPhoneCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $confirmationUuid,
        public readonly string $inputCode,
    ) {}
}