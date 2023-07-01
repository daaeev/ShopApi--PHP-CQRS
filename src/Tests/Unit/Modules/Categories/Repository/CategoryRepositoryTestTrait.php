<?php

namespace Project\Tests\Unit\Modules\Categories\Repository;

use Project\Common\Utils\DateTimeFormat;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Categories\Entity\Category;
use Project\Modules\Catalogue\Categories\Entity\CategoryId;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Catalogue\Categories\Repository\CategoryRepositoryInterface;

trait CategoryRepositoryTestTrait
{
    use CategoryFactory, ProductFactory;

    protected CategoryRepositoryInterface $categories;
    protected ProductRepositoryInterface $products;

    public function testAdd()
    {
        $parent = $this->generateCategory();
        $product = $this->generateProduct();
        $this->products->add($product);
        $this->categories->add($parent);

        $initial = $this->generateCategory();
        $initial->attachParent($parent->getId());
        $initial->attachProduct($product->getId()->getId());
        $this->categories->add($initial);
        $found = $this->categories->get($initial->getId());
        $this->assertSameCategories($initial, $found);
    }

    private function assertSameCategories(Category $initial, Category $found): void
    {
        $this->assertTrue($initial->getId()->equalsTo($found->getId()));
        if (($initial->getParent() !== null) && ($found->getParent() !== null)) {
            $this->assertTrue($initial->getParent()->equalsTo($found->getParent()));
        }
        $this->assertEquals($initial->getName(), $found->getName());
        $this->assertEquals($initial->getSlug(), $found->getSlug());
        $this->assertEquals($initial->getProducts(), $found->getProducts());
        $this->assertSame(
            $initial->getCreatedAt()->format(DateTimeFormat::FULL_DATE->value),
            $found->getCreatedAt()->format(DateTimeFormat::FULL_DATE->value),
        );
        $this->assertSame(
            $initial->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE->value),
            $found->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE->value),
        );
    }

    public function testAddIncrementIds()
    {
        $category = $this->makeCategory(
            CategoryId::next(),
            md5(rand()),
            md5(rand()),
        );
        $this->assertNull($category->getId()->getId());
        $this->categories->add($category);
        $this->assertNotNull($category->getId()->getId());
    }

    public function testAddIfAlreadyExists()
    {
        $category = $this->generateCategory();
        $secondProduct = $this->makeCategory(
            $category->getId(),
            $category->getName(),
            'Unique category slug',
        );
        $this->categories->add($category);
        $this->expectException(DuplicateKeyException::class);
        $this->categories->add($secondProduct);
    }

    public function testAddWithNotUniqueSlug()
    {
        $category = $this->generateCategory();
        $categoryWithNotUniqueCode = $this->generateCategory();
        $categoryWithNotUniqueCode->updateSlug($category->getSlug());
        $this->categories->add($category);
        $this->expectException(DuplicateKeyException::class);
        $this->categories->add($categoryWithNotUniqueCode);
    }

    public function testUpdate()
    {
        $parent = $this->generateCategory();
        $product = $this->generateProduct();
        $this->products->add($product);
        $this->categories->add($parent);

        $initial = $this->generateCategory();
        $this->categories->add($initial);
        $added = $this->categories->get($initial->getId());
        $added->updateSlug(md5(rand()));
        $added->updateName(md5(rand()));
        $added->attachParent($parent->getId());
        $added->attachProduct($product->getId()->getId());
        $this->categories->update($added);
        $updated = $this->categories->get($initial->getId());
        $this->assertSameCategories($added, $updated);
        $this->assertNotSame($initial->getSlug(), $updated->getSlug());
        $this->assertNotSame($initial->getName(), $updated->getName());
        $this->assertNotSame($initial->getProducts(), $updated->getProducts());
        $this->assertNull($initial->getParent());
        $this->assertNull($initial->getUpdatedAt());
    }

    public function testUpdateIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $category = $this->generateCategory();
        $this->categories->update($category);
    }

    public function testUpdateWithNotUniqueSlug()
    {
        $category = $this->generateCategory();
        $categoryWithNotUniqueCode = $this->generateCategory();
        $this->categories->add($category);
        $this->categories->add($categoryWithNotUniqueCode);
        $categoryWithNotUniqueCode->updateSlug($category->getSlug());
        $this->expectException(DuplicateKeyException::class);
        $this->categories->update($categoryWithNotUniqueCode);
    }

    public function testDelete()
    {
        $category = $this->generateCategory();
        $this->categories->add($category);
        $this->categories->delete($category);
        $this->expectException(NotFoundException::class);
        $this->categories->get($category->getId());
    }

    public function testDeleteIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $category = $this->generateCategory();
        $this->categories->delete($category);
    }

    public function testGet()
    {
        $category = $this->generateCategory();
        $this->categories->add($category);
        $found = $this->categories->get($category->getId());
        $this->assertSameCategories($category, $found);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->categories->get(CategoryId::random());
    }
}