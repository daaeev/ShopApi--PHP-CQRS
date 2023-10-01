<?php

namespace Project\Tests\Unit\Entity\Id;

use Project\Common\Entity\Id\AutoIncrementId;

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

        $randomId1 = AutoIncrementId::random();
        $randomId2 = AutoIncrementId::random();
        $this->assertIsInt($randomId1->getId());
        $this->assertIsInt($randomId2->getId());
        $this->assertNotEquals($randomId1, $randomId2);
    }
}