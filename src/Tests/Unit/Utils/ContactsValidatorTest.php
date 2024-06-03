<?php

namespace Project\Tests\Unit\Utils;

use Project\Common\Utils\ContactsValidator;

class ContactsValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testValidatePhone()
    {
        $this->expectNotToPerformAssertions();
        ContactsValidator::validatePhone('+380501234567');
    }

    public function testValidateNotValidPhone()
    {
        $this->expectException(\DomainException::class);
        ContactsValidator::validatePhone('+3801234');
    }

    public function testValidateEmail()
    {
        $this->expectNotToPerformAssertions();
        ContactsValidator::validateEmail('test@test.com');
    }

    public function testValidateNotValidEmail()
    {
        $this->expectException(\DomainException::class);
        ContactsValidator::validateEmail('test@test');
    }
}