<?php

namespace Project\Tests\Unit\Entity\Collections;

use PHPUnit\Framework\TestCase;
use Project\Common\Entity\Collections\Pagination;
use Project\Common\Entity\Collections\PaginatedCollection;

class PaginatedCollectionTest extends TestCase
{
    public function testPaginationToArray()
    {
        $pagination = new Pagination(1, 15, 20);
        $this->assertSame(['page' => 1, 'limit' => 15, 'total' => 20], $pagination->toArray());
    }

    public function testToArray()
    {
        $pagination = new Pagination(1, 15, 20);
        $collection = new PaginatedCollection([1, 2, 3], $pagination);
        $expected = [
            'items' => [1, 2, 3],
            'pagination' => ['page' => 1, 'limit' => 15, 'total' => 20]
        ];

        $this->assertSame($expected, $collection->toArray());
    }
}