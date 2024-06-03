<?php

namespace Project\Tests\Unit\Utils;

use Project\Common\Utils\PhoneHelper;

class PhoneHelperTest extends \PHPUnit\Framework\TestCase
{
    private string $normalizedPhone = '+380661234567';

    private array $formattedPhones = [
        '+38 (066) 123 45 67',
        '+38 066 123 45 67',
        '+380661234567',
        '380661234567',
        '0661234567',
        '066 123 45 67',
    ];

    public function testNormalizePhone()
    {
        foreach ($this->formattedPhones as $phone) {
            $this->assertSame($this->normalizedPhone, PhoneHelper::normalize($phone));
        }
    }

    public function testNormalizeNotValidPhone()
    {
        $this->expectException(\DomainException::class);
        PhoneHelper::normalize('+12345');
    }

    public function testValidatePhone()
    {
        foreach ($this->formattedPhones as $phone) {
            $this->expectNotToPerformAssertions();
            PhoneHelper::validate($phone);
        }
    }

    public function testValidateNotValidPhone()
    {
        $this->expectException(\DomainException::class);
        PhoneHelper::validate('+12345');
    }
}