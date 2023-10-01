<?php

namespace Project\Tests\Unit\Modules\Categories\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Categories\Entity\CategoryId;
use Project\Modules\Catalogue\Api\Events\Category\CategoryUpdated;

class ParentTest extends \PHPUnit\Framework\TestCase
{
    use CategoryFactory, AssertEvents;

    public function testAttachParent()
    {
        $category = $this->generateCategory();
        $category->attachParent($parent = CategoryId::random());
        $this->assertNotEmpty($category->getUpdatedAt());
        $this->assertTrue($parent->equalsTo($category->getParent()));
        $this->assertEvents($category, [new CategoryUpdated($category)]);
    }

    public function testAttachParentToSame()
    {
        $category = $this->generateCategory();
        $category->attachParent($parent = CategoryId::random());
        $category->flushEvents();
        $category->attachParent($parent);
        $this->assertTrue($parent->equalsTo($category->getParent()));
        $this->assertEmpty($category->flushEvents());
    }

    public function testAttachCurrenctCategoryAsParent()
    {
        $this->expectException(\DomainException::class);
        $category = $this->generateCategory();
        $category->attachParent($category->getId());
    }

    public function testDetachParent()
    {
        $category = $this->generateCategory();
        $category->attachParent(CategoryId::random());
        $category->detachParent();
        $this->assertNull($category->getParent());
        $this->assertEvents($category, [new CategoryUpdated($category)]);
    }

    public function testDetachParentIfParentDoesNotAttached()
    {
        $category = $this->generateCategory();
        $category->detachParent();
        $this->assertNull($category->getParent());
        $this->assertEmpty($category->flushEvents());
    }
}