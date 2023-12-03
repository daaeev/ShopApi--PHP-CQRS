<?php

namespace Project\Tests\Unit\Modules\Client\Entity;

use Project\Modules\Client\Entity\Contacts;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;

class ClientContactsObjectTest extends \PHPUnit\Framework\TestCase
{
    use ContactsGenerator;

    public function testCreate()
    {
        $contacts = new Contacts();
        $this->assertNull($contacts->getPhone());
        $this->assertNull($contacts->getEmail());
        $this->assertFalse($contacts->isPhoneConfirmed());
        $this->assertFalse($contacts->isEmailConfirmed());

        $contacts = new Contacts(
            $phone = $this->generatePhone(),
            $email = $this->generateEmail(),
            true,
            true
        );
        $this->assertEquals($phone, $contacts->getPhone());
        $this->assertEquals($email, $contacts->getEmail());
        $this->assertTrue($contacts->isPhoneConfirmed());
        $this->assertTrue($contacts->isEmailConfirmed());
    }

    public function testCreateWithEmptyConfirmedPhone()
    {
        $this->expectException(\DomainException::class);
        new Contacts(phoneConfirmed: true);
    }

    public function testCreateWithEmptyConfirmedEmail()
    {
        $this->expectException(\DomainException::class);
        new Contacts(emailConfirmed: true);
    }

    /**
     * @dataProvider equalsContacts
     */
    public function testEquals(Contacts $contacts1, Contacts $contacts2)
    {
        $this->assertTrue($contacts1->equalsTo($contacts2));
    }

    public static function equalsContacts(): array
    {
        $phone = '+380123456789';
        $email = 'testequals@gmail.com';
        return [
            [new Contacts, new Contacts],
            [new Contacts($phone), new Contacts($phone)],
            [new Contacts($phone, $email), new Contacts($phone, $email)],
            [
                new Contacts($phone, $email, true),
                new Contacts($phone, $email, true)
            ],
            [
                new Contacts($phone, $email, true, true),
                new Contacts($phone, $email, true, true)
            ],
        ];
    }

    /**
     * @dataProvider notEqualsContacts
     */
    public function testNotEquals(Contacts $contacts1, Contacts $contacts2)
    {
        $this->assertFalse($contacts1->equalsTo($contacts2));
    }

    public static function notEqualsContacts(): array
    {
        $phone1 = '+380123456789';
        $phone2 = '+380123456788';
        $email1 = 'testequals1@gmail.com';
        $email2 = 'testequals2@gmail.com';
        return [
            [new Contacts, new Contacts($phone1)],
            [new Contacts($phone1), new Contacts($phone1, $email1)],
            [new Contacts($phone1, $email1), new Contacts($phone1, $email2)],
            [new Contacts($phone1, $email1), new Contacts($phone2, $email1)],
            [
                new Contacts($phone1, $email1),
                new Contacts($phone1, $email1, true)
            ],
            [
                new Contacts($phone1, $email1, true),
                new Contacts($phone1, $email1, true, true)
            ],
            [
                new Contacts($phone1, $email1, true, true),
                new Contacts($phone1, $email1, false, true)
            ],
            [
                new Contacts($phone1, $email1, true, true),
                new Contacts($phone1, $email1, true, false)
            ],
        ];
    }
}