<?php

namespace Project\Tests\Unit\Modules\Categories\Entity;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Api\Events\Category\CategoryUpdated;

class SlugTest extends \PHPUnit\Framework\TestCase
{
    use CategoryFactory, AssertEvents;

    public function testUpdateSlug()
    {
        $category = $this->generateCategory();
        $this->assertNull($category->getUpdatedAt());
        $updatedSlug = md5(rand());
        $this->assertNotSame($updatedSlug, $category->getSlug());
        $category->updateSlug($updatedSlug);
        $this->assertNotNull($category->getUpdatedAt());
        $this->assertSame($updatedSlug, $category->getSlug());
        $this->assertEvents($category, [new CategoryUpdated($category)]);
    }

    public function testUpdateSlugToSame()
    {
        $category = $this->generateCategory();
        $this->assertNull($category->getUpdatedAt());
        $updatedSlug = $category->getSlug();
        $this->assertSame($updatedSlug, $category->getSlug());
        $category->updateSlug($updatedSlug);
        $this->assertNull($category->getUpdatedAt());
        $this->assertSame($updatedSlug, $category->getSlug());
        $this->assertEmpty($category->flushEvents());
    }

    public function testUpdateSlugToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generateCategory()->updateSlug('');
    }
}