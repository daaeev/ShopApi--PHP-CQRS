<?php

namespace Project\Tests\Unit\Entity\Collections;

use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;
use Project\Tests\Unit\Entity\Collections\Entities\Stringable;
use Project\Tests\Unit\Entity\Collections\Entities\NotStringable;

class PaginatedCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testPaginationToArray()
    {
        $pagination = new Pagination(1, 15, 20);
        $this->assertSame([
            'page' => 1,
            'limit' => 15,
            'total' => 20,
        ], $pagination->toArray());
    }

    public function testToArray()
    {
        $pagination = new Pagination(1, 15, 20);
        $data = [
            1,
            'test',
            [
                new Stringable
            ],
            new NotStringable
        ];
        $collection = new PaginatedCollection($data, $pagination);
        $this->assertSame([
            'items' => [
                1,
                'test',
                [
                    'Stringable'
                ],
                'Item #3'
            ],
            'pagination' => [
                'page' => 1,
                'limit' => 15,
                'total' => 20,
            ]
        ], $collection->toArray());
    }
}