<?php

namespace Project\Tests\Unit\Entity\Id;

use Project\Common\Entity\Id\StringId;

class StringIdTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $id = new StringId('test');
        $this->assertEquals('test', $id->getId());
        $this->assertTrue($id->equalsTo(new StringId('test')));
        $this->assertfalse($id->equalsTo(new StringId('test2')));

        $nullableId = new StringId(null);
        $this->assertNull($nullableId->getId());
        $this->assertFalse($nullableId->equalsTo(new StringId(null)));
        $this->assertFalse($nullableId->equalsTo(new StringId('test')));

        $randomId1 = StringId::random();
        $randomId2 = StringId::random();
        $this->assertIsString($randomId1->getId());
        $this->assertIsString($randomId2->getId());
        $this->assertNotEquals($randomId1, $randomId2);
    }
}