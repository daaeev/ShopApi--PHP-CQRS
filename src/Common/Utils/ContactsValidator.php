<?php

namespace Project\Common\Utils;

class ContactsValidator
{
    public static function validatePhone(?string $phone): void
    {
        if (!empty($phone)) {
            PhoneHelper::validate($phone);
        }
    }

    public static function validateEmail(?string $email): void
    {
        if (empty($email)) {
            return;
        }

        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException("Email '$email' is not valid");
        }
    }
}