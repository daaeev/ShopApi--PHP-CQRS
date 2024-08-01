<?php

namespace Project\Common\Utils;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class PhoneHelper
{
    public static function normalize(string $phone): string
    {
        $util = PhoneNumberUtil::getInstance();

        try {
            $phoneProto = $util->parse($phone, CountryCodeIso3166::UKRAINE);
        } catch (NumberParseException) {
            throw new \DomainException("Phone '$phone' is not valid");
        }

        if (!$util->isPossibleNumber($phoneProto, CountryCodeIso3166::UKRAINE)) {
            throw new \DomainException("Phone '$phone' is not valid");
        }

        return $util->format($phoneProto, PhoneNumberFormat::E164);
    }

    public static function validate(string $phone): void
    {
        $normalized = self::normalize($phone);
        $util = PhoneNumberUtil::getInstance();

        try {
            $parsed = $util->parse($normalized, CountryCodeIso3166::UKRAINE);
        } catch (NumberParseException) {
            throw new \DomainException("Phone '$phone' is not valid");
        }

        if (!$util->isPossibleNumber($parsed)) {
            throw new \DomainException("Phone '$phone' is not valid");
        }
    }
}