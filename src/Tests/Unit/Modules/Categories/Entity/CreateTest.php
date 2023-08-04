<?php

namespace Project\Tests\Unit\Modules\Categories\Entity;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Categories\Entity\CategoryId;
use Project\Modules\Catalogue\Api\Events\Category\CategoryCreated;

class CreateTest extends \PHPUnit\Framework\TestCase
{
    use CategoryFactory, AssertEvents;

    public function testCreate()
    {
        $category = $this->makeCategory(
            $id = CategoryId::random(),
            $name = md5(rand()),
            $slug = md5(rand()),
        );

        $this->assertTrue($category->getId()->equalsTo($id));
        $this->assertSame($name, $category->getName());
        $this->assertSame($slug, $category->getSlug());
        $this->assertEmpty($category->getProducts());
        $this->assertNull($category->getParent());
        $this->assertNotEmpty($category->getCreatedAt());
        $this->assertNull($category->getUpdatedAt());
        $this->assertEvents($category, [new CategoryCreated($category)]);
    }

    public function testCreateWithEmptyName()
    {
        $this->expectException(InvalidArgumentException::class);
         $this->makeCategory(
            CategoryId::random(),
            '',
            md5(rand()),
        );
    }

    public function testCreateWithEmptySlug()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeCategory(
            CategoryId::random(),
            md5(rand()),
            '',
        );
    }
}