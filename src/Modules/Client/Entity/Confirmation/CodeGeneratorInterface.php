<?php

namespace Project\Modules\Client\Entity\Confirmation;

interface CodeGeneratorInterface
{
    public function generate(): int|string;
}