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

        $randomId1 = StringId::random();
        $randomId2 = StringId::random();
        $this->assertNotEquals($randomId1, $randomId2);

        $this->expectException(\InvalidArgumentException::class);
        new StringId('');
    }
}