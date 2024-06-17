<?php

namespace Project\Tests\Unit\Modules\Client\Entity;

use Project\Modules\Client\Entity\Contacts;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;

class ClientContactsObjectTest extends \PHPUnit\Framework\TestCase
{
    use ContactsGenerator;

    public function testCreate()
    {
        $contacts = new Contacts(
            $phone = $this->generatePhone(),
            $email = $this->generateEmail(),
            phoneConfirmed: true,
            emailConfirmed: true
        );

        $this->assertSame($phone, $contacts->getPhone());
        $this->assertSame($email, $contacts->getEmail());
        $this->assertTrue($contacts->isPhoneConfirmed());
        $this->assertTrue($contacts->isEmailConfirmed());
    }

    public function testCreateWithEmptyPhone()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Contacts(phone: '');
    }

    public function testCreateWithInvalidPhone()
    {
        $this->expectException(\DomainException::class);
        new Contacts(phone: '+3801234');
    }

    public function testCreateWithInvalidEmail()
    {
        $this->expectException(\DomainException::class);
        new Contacts(phone: $this->generatePhone(), email: 'test@test');
    }

    public function testCreateWithEmptyConfirmedEmail()
    {
        $this->expectException(\DomainException::class);
        new Contacts(phone: $this->generatePhone(), emailConfirmed: true);
    }

    public function testUpdateEmail()
    {
        $contacts = new Contacts(
            $this->generatePhone(),
            $initialEmail = $this->generateEmail(),
            emailConfirmed: true
        );

        $updated = $contacts->updateEmail($email = $this->generateEmail());
        $this->assertSame($initialEmail, $contacts->getEmail());
        $this->assertSame($email, $updated->getEmail());
        $this->assertFalse($updated->isEmailConfirmed());
    }

    public function testUpdateEmailToInvalid()
    {
        $contacts = new Contacts($this->generatePhone(), $this->generateEmail(), emailConfirmed: true);
        $this->expectException(\DomainException::class);
        $contacts->updateEmail('test@test');
    }

    public function testConfirmPhone()
    {
        $contacts = new Contacts($this->generatePhone());
        $updated = $contacts->confirmPhone();
        $this->assertFalse($contacts->isPhoneConfirmed());
        $this->assertTrue($updated->isPhoneConfirmed());
    }

    public function testConfirmPhoneIfPhoneAlreadyConfirmed()
    {
        $contacts = new Contacts($this->generatePhone(), phoneConfirmed: true);
        $this->expectException(\DomainException::class);
        $contacts->confirmPhone();
    }

    public function testConfirmEmail()
    {
        $contacts = new Contacts($this->generatePhone(), $this->generateEmail());
        $updated = $contacts->confirmEmail();
        $this->assertFalse($contacts->isEmailConfirmed());
        $this->assertTrue($updated->isEmailConfirmed());
    }

    public function testConfirmEmptyEmail()
    {
        $contacts = new Contacts($this->generatePhone());
        $this->expectException(\DomainException::class);
        $contacts->confirmEmail();
    }

    public function testConfirmEmailIfEmailAlreadyConfirmed()
    {
        $contacts = new Contacts($this->generatePhone(), $this->generateEmail(), emailConfirmed: true);
        $this->expectException(\DomainException::class);
        $contacts->confirmEmail();
    }

    public function testEquals()
    {
        $contacts1 = new Contacts($this->generatePhone(), $this->generateEmail());
        $contacts2 = new Contacts($contacts1->getPhone(), $contacts1->getEmail());
        $this->assertTrue($contacts1->equalsTo($contacts2));
    }

    public function testNotEquals()
    {
        $contacts1 = new Contacts($this->generatePhone(), $this->generateEmail());
        $contacts2 = new Contacts($this->generatePhone(), $this->generateEmail());
        $this->assertFalse($contacts1->equalsTo($contacts2));

        $contacts1 = new Contacts($this->generatePhone(), $this->generateEmail());
        $contacts2 = new Contacts($contacts1->getPhone(), $contacts1->getEmail(), phoneConfirmed: true);
        $this->assertFalse($contacts1->equalsTo($contacts2));
    }
}
