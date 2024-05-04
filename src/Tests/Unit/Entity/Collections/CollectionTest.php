<?php

namespace Project\Tests\Unit\Entity\Collections;

use Project\Common\Utils\Arrayable;
use Project\Common\Entity\Collections\Collection;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testWithNumericKeys()
    {
        $nums = range(1, 5);
        $collection = new Collection($nums);
        foreach ($collection as $key => $value) {
            $this->assertSame($nums[$key], $value);
        }

        $this->assertSame($nums, $collection->all());
    }

    public function testWithStringKeys()
    {
        $nums = ['test1' => 1, 'test2' => 2, 'test3' => 2];
        $collection = new Collection($nums);
        $keyAsNum = 0;
        foreach ($collection as $key => $value) {
            $this->assertSame($keyAsNum++, $key);
            $this->assertArrayNotHasKey($key, $nums);
        }

        $this->assertSame(array_values($nums), $collection->all());
        $this->assertSame(range(0, 2), array_keys($collection->all()));
    }

    public function testToArrayWithScalarValues()
    {
        $data = [1, true, 'test'];
        $collection = new Collection($data);
        $this->assertSame($data, $collection->toArray());
        $this->assertSame($data, $collection->all());
    }

    public function testToArrayWithStringableObjects()
    {
        $data = [new Mock\Stringable];
        $collection = new Collection($data);
        $this->assertSame(['Stringable'], $collection->toArray());
        $this->assertSame($data, $collection->all());
    }

    public function testToArrayWithArrayableObjects()
    {
        $dto = $this->getMockBuilder(Arrayable::class)->getMock();
        $dto->expects($this->once())
            ->method('toArray')
            ->willReturn([1, 2, 3]);

        $data = [$dto];
        $collection = new Collection($data);
        $this->assertSame([[1, 2, 3]], $collection->toArray());
        $this->assertSame($data, $collection->all());
    }

    public function testToArrayWithArrayItems()
    {
        $dto = $this->getMockBuilder(Arrayable::class)->getMock();
        $dto->expects($this->once())
            ->method('toArray')
            ->willReturn([1, 2, 3]);

        $data = [[1], [$dto]];
        $collection = new Collection($data);

        $expected = [[1], [[1, 2, 3]]];
        $this->assertSame($expected, $collection->toArray());
        $this->assertSame($data, $collection->all());
    }

    public function testToArrayWithNotStringableOrArrayableObjects()
    {
        $data = [new \stdClass, new \stdClass];
        $collection = new Collection($data);
        $this->assertSame(['Item #0', 'Item #1'], $collection->toArray());
        $this->assertSame($data, $collection->all());
    }

    public function testCloneWithScalarItems()
    {
        $data = [1, true, 'string'];
        $collection = new Collection($data);
        $cloned = clone $collection;
        $this->assertSame($data, $cloned->all());
    }

    public function testCloneWithObjectsItems()
    {
        $data = [new \stdClass];
        $collection = new Collection($data);
        $cloned = clone $collection;
        $this->assertNotSame($data, $cloned->all());
    }
}