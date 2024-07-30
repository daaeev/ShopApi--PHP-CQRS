<?php

namespace Project\Tests\Unit\Entity\Id;

use Project\Common\Entity\Id\IntegerId;

class IntegerTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $id = new IntegerId($int = random_int(1, 999999));
        $this->assertEquals($int, $id->getId());
        $this->assertTrue($id->equalsTo(new IntegerId($int)));
        $this->assertfalse($id->equalsTo(new IntegerId(random_int(1, 999999))));

        $randomId1 = IntegerId::random();
        $randomId2 = IntegerId::random();
        $this->assertNotEquals($randomId1, $randomId2);

        $this->expectException(\InvalidArgumentException::class);
        new IntegerId(0);
    }
}