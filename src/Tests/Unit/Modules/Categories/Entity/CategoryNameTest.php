<?php

namespace Project\Tests\Unit\Modules\Categories\Entity;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Api\Events\Category\CategoryUpdated;

class CategoryNameTest extends \PHPUnit\Framework\TestCase
{
    use CategoryFactory, AssertEvents;

    public function testUpdateName()
    {
        $category = $this->generateCategory();
        $updatedName = md5(rand());
        $category->updateName($updatedName);
        $this->assertNotNull($category->getUpdatedAt());
        $this->assertSame($updatedName, $category->getName());
        $this->assertEvents($category, [new CategoryUpdated($category)]);
    }

    public function testUpdateNameToSame()
    {
        $category = $this->generateCategory();
        $updatedName = $category->getName();
        $category->updateName($updatedName);
        $this->assertNull($category->getUpdatedAt());
        $this->assertSame($updatedName, $category->getName());
        $this->assertEmpty($category->flushEvents());
    }

    public function testUpdateNameToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generateCategory()->updateName('');
    }
}