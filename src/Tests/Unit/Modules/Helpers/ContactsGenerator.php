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
        $email = substr(md5(rand()), 0, 10);
        $email .= '@gmail.com';
        return $email;
    }
}