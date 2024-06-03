<?php

namespace Project\Tests\Unit\Modules\Helpers;

trait ContactsGenerator
{
    private function generatePhone(): string
    {
        $phone = '+380';
        for ($iteration = 1; $iteration <= 9; $iteration++) {
            $phone .= rand(0, 9);
        }

        return $phone;
    }

    private function generateEmail(): string
    {
        $email = uniqid();
        $email .= '@test.com';
        return $email;
    }
}