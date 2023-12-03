<?php

namespace Project\Tests\Unit\Modules\Categories\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Api\Events\Category\CategoryUpdated;

class CategoryProductsTest extends \PHPUnit\Framework\TestCase
{
    use CategoryFactory, AssertEvents;

    public function testAttachProduct()
    {
        $category = $this->generateCategory();
        $products = [1, 2, 3];
        $category->attachProduct($products[0]);
        $category->attachProduct($products[1]);
        $category->attachProduct($products[2]);
        $this->assertSame($products, $category->getProducts());
        $this->assertNotEmpty($category->getUpdatedAt());
        $this->assertEvents($category, [new CategoryUpdated($category)]);
    }

    public function testAttachProductIfAlreadyAttached()
    {
        $category = $this->generateCategory();
        $category->attachProduct(1);
        $this->assertCount(1, $category->getProducts());
        $category->flushEvents();
        $category->attachProduct(1);
        $this->assertCount(1, $category->getProducts());
        $this->assertEmpty($category->flushEvents());
    }

    public function testDetachProducts()
    {
        $category = $this->generateCategory();
        $products = [1, 2, 3];
        $category->attachProduct($products[0]);
        $category->attachProduct($products[1]);
        $category->attachProduct($products[2]);
        $this->assertSame($products, $category->getProducts());
        $category->flushEvents();
        $category->detachProducts();
        $this->assertEmpty($category->getProducts());
        $this->assertEvents($category, [new CategoryUpdated($category)]);
    }

    public function testDetachProductsIfProductsDoesNotAttached()
    {
        $category = $this->generateCategory();
        $category->detachProducts();
        $this->assertEmpty($category->getProducts());
        $this->assertEmpty($category->flushEvents());
    }
}