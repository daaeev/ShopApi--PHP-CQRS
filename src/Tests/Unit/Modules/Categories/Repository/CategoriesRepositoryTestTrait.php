<?php

namespace Project\Tests\Unit\Modules\Categories\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Categories\Entity\CategoryId;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;
use Project\Modules\Catalogue\Categories\Repository\CategoriesRepositoryInterface;

trait CategoriesRepositoryTestTrait
{
    use CategoryFactory, ProductFactory;

    protected CategoriesRepositoryInterface $categories;
    protected ProductsRepositoryInterface $products;

    public function testAdd()
    {
        $parent = $this->generateCategory();
        $product = $this->generateProduct();
        $this->products->add($product);
        $this->categories->add($parent);

        $initial = $this->generateCategory();
        $initial->attachParent($parent->getId());
        $initial->attachProduct($product->getId()->getId());

        $id = $initial->getId();
        $name = $initial->getName();
        $slug = $initial->getSlug();
        $products = $initial->getProducts();
        $parentId = $initial->getParent()->getId();
        $createdAt = $initial->getCreatedAt();
        $updatedAt = $initial->getUpdatedAt();

        $this->categories->add($initial);

        $found = $this->categories->get($initial->getId());
        $this->assertSame($initial, $found);
        $this->assertTrue($found->getId()->equalsTo($id));
        $this->assertSame($found->getName(), $name);
        $this->assertSame($found->getSlug(), $slug);
        $this->assertSame($found->getProducts(), $products);
        $this->assertSame($found->getParent()->getId(), $parentId);
        $this->assertSame($found->getCreatedAt()->getTimestamp(), $createdAt->getTimestamp());
        $this->assertSame($found->getUpdatedAt()->getTimestamp(), $updatedAt->getTimestamp());
    }

    public function testAddIncrementIds()
    {
        $category = $this->makeCategory(
            CategoryId::next(),
            md5(rand()),
            md5(rand()),
        );

        $this->categories->add($category);
        $this->assertNotNull($category->getId()->getId());
    }

    public function testAddWithDuplicatedId()
    {
        $category = $this->generateCategory();
        $categoryWithSameId = $this->makeCategory(
            $category->getId(),
            $category->getName(),
            'Unique category slug',
        );

        $this->categories->add($category);
        $this->expectException(DuplicateKeyException::class);
        $this->categories->add($categoryWithSameId);
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

        $added->updateSlug($slug = md5(rand()));
        $added->updateName($name = md5(rand()));
        $added->attachParent($parent->getId());
        $added->attachProduct($product->getId()->getId());
        $parentId = $parent->getId()->getId();
        $products = $added->getProducts();
        $createdAt = $added->getCreatedAt();
        $updatedAt = $added->getUpdatedAt();
        $this->categories->update($added);

        $updated = $this->categories->get($initial->getId());
        $this->assertSame($initial, $added);
        $this->assertSame($added, $updated);
        $this->assertSame($updated->getSlug(), $slug);
        $this->assertSame($updated->getName(), $name);
        $this->assertSame($updated->getParent()->getId(), $parentId);
        $this->assertSame($updated->getProducts(), $products);
        $this->assertSame($updated->getCreatedAt()->getTimestamp(), $createdAt->getTimestamp());
        $this->assertSame($updated->getUpdatedAt()->getTimestamp(), $updatedAt->getTimestamp());
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

    public function testUpdateCategoryAndDoesNotChangeSlug()
    {
        $category = $this->generateCategory();
        $this->categories->add($category);
        $this->categories->update($category);
        $this->expectNotToPerformAssertions();
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

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->categories->get(CategoryId::random());
    }
}
