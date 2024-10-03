<?php

namespace Project\Tests\Unit\Entity\Id;

use Project\Common\Entity\Id\Uuid;
use Ramsey\Uuid\Uuid as RamseyUuid;

class UuidTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $generated = RamseyUuid::uuid4();
        $id = new Uuid($generated);
        $this->assertEquals($generated->toString(), $id->getId());
        $this->assertTrue($id->equalsTo(new Uuid($generated)));
        $this->assertfalse($id->equalsTo(new Uuid(RamseyUuid::uuid4())));

        $randomId1 = Uuid::random();
        $randomId2 = Uuid::random();
        $this->assertIsString($randomId1->getId());
        $this->assertIsString($randomId2->getId());
        $this->assertNotEquals($randomId1, $randomId2);
    }
}