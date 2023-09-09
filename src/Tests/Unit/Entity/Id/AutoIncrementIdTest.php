<?php

namespace Project\Tests\Unit\Entity\Id;

use Project\Common\Entity\Id\AutoIncrementId;
use TypeError;

class AutoIncrementIdTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $id = new AutoIncrementId(23);

        $this->assertEquals(23, $id->getId());
        $this->assertTrue($id->equalsTo(new AutoIncrementId(23)));
        $this->assertfalse($id->equalsTo(new AutoIncrementId(1)));

        $nullableId = new AutoIncrementId(null);

        $this->assertNull($nullableId->getId());
        $this->assertFalse($nullableId->equalsTo(new AutoIncrementId(null)));
        $this->assertFalse($nullableId->equalsTo(new AutoIncrementId(1)));

        $nextId = AutoIncrementId::next();

        $this->assertNull($nextId->getId());

        $randomId = AutoIncrementId::random();

        $this->assertIsInt($randomId->getId());
    }

    public function testCreateWithNotIntegerIdValue()
    {
        $this->expectException(TypeError::class);
        new AutoIncrementId('Invalid argument');
    }
}