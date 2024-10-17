<?php

namespace Project\Modules\Client\Entity\Confirmation;

class DigitCodeGenerator implements CodeGeneratorInterface
{
    public function generate(): int|string
    {
        return random_int(1000, 9999);
    }
}