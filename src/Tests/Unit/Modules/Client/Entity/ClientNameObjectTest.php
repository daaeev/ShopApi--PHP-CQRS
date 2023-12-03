<?php

namespace Project\Tests\Unit\Modules\Client\Entity;

use Project\Modules\Client\Entity\Name;

class ClientNameObjectTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $name = new Name();
        $this->assertNull($name->getFirstName());
        $this->assertNull($name->getLastName());
        $this->assertNull($name->getFullName());

        $name = new Name('FirstName', 'LastName');
        $this->assertEquals('FirstName', $name->getFirstName());
        $this->assertEquals('LastName', $name->getLastName());
        $this->assertEquals('FirstName LastName', $name->getFullName());

        $name = new Name('FirstName');
        $this->assertEquals('FirstName', $name->getFirstName());
        $this->assertNull($name->getLastName());
        $this->assertEquals('FirstName', $name->getFullName());
    }

    public function testCreateOnlyWithLastName()
    {
        $this->expectException(\DomainException::class);
        new Name(lastName: 'LastName');
    }

    /**
     * @dataProvider equalsNames
     */
    public function testEquals(Name $name1, Name $name2)
    {
        $this->assertTrue($name1->equalsTo($name2));
    }

    public static function equalsNames(): array
    {
        return [
            [new Name, new Name],
            [new Name('FirstName'), new Name('FirstName')],
            [
                new Name('FirstName', 'LastName'),
                new Name('FirstName', 'LastName')
            ],
        ];
    }

    /**
     * @dataProvider notEqualsNames
     */
    public function testNotEquals(Name $name1, Name $name2)
    {
        $this->assertFalse($name1->equalsTo($name2));
    }

    public static function notEqualsNames(): array
    {
        return [
            [new Name, new Name('FirstName')],
            [
                new Name('FirstName'),
                new Name('FirstName', 'LastName')
            ],
            [
                new Name('FirstName1', 'LastName'),
                new Name('FirstName2', 'LastName')
            ],
            [
                new Name('FirstName', 'LastName1'),
                new Name('FirstName', 'LastName2')
            ],
        ];
    }
}