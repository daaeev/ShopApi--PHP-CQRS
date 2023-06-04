<?php

namespace Project\Tests\Unit\Entity\Collections;

use Project\Tests\Unit\Entity\Collections\Entities\TestDTO;
use Project\Common\Entity\Collections\Collection;
use Project\Tests\Unit\Entity\Collections\Entities\Stringable;
use Project\Tests\Unit\Entity\Collections\Entities\NotStringable;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testForEach()
    {
        $data = range(1, 5);
        $collectionData = [];
        $collection = new Collection($data);

        foreach ($collection as $key => $value) {
            $collectionData[$key] = $value;
            $this->assertSame($data[$key], $value);
        }

        $this->assertSame($data, $collectionData);
    }

    public function testCheckCollectionItemsKeys()
    {
        $data = [
            'test1' => 1,
            'test2' => 2,
            'test3' => 2,
        ];
        $collection = new Collection($data);

        $iter = 0;
        foreach ($collection as $key => $value) {
            $this->assertSame($iter++, $key);
            $this->assertArrayNotHasKey($key, $data);
        }
    }

    public function testToArrayWithScalarValues()
    {
        $data = [1, true, 'test'];
        $collection = new Collection($data);
        $this->assertSame($data, $collection->toArray());
    }

    public function testToArrayWithStringableItems()
    {
        $data = [new Stringable];
        $collection = new Collection($data);
        $this->assertSame(['Stringable'], $collection->toArray());
    }

    public function testToArrayWithArrayableItems()
    {
        $dto = ['test', 'dto'];
        $data = [new TestDTO($dto)];
        $collection = new Collection($data);
        $this->assertSame([$dto], $collection->toArray());
    }

    public function testToArrayWithArrayItems()
    {
        $dto = ['test', 'dto'];
        $data = [
            [1],
            ['test'],
            [
                new TestDTO($dto),
                new Stringable,
                new NotStringable
            ],
        ];
        $collection = new Collection($data);
        $this->assertSame([
            [1],
            ['test'],
            [
                $dto,
                'Stringable',
                'Item #2'
            ]
        ], $collection->toArray());
    }

    public function testToArrayWithNotStringableItems()
    {
        $data = [
            1,
            new NotStringable,
            new NotStringable,
            'test',
            new NotStringable,
        ];
        $collection = new Collection($data);
        $this->assertSame([
            1,
            'Item #1',
            'Item #2',
            'test',
            'Item #4'
        ], $collection->toArray());

    }
}