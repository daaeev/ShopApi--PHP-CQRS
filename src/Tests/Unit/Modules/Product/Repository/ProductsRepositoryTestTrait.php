<?php

namespace Project\Tests\Unit\Modules\Product\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Catalogue\Product\Entity\Product;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;

trait ProductsRepositoryTestTrait
{
    use ProductFactory;

    protected ProductsRepositoryInterface $products;

    public function testAdd()
    {
        $initial = $this->generateProduct();
        $initial->setColors([
            md5(rand()),
            md5(rand()),
        ]);
        $initial->setSizes([
            md5(rand())
        ]);
        $this->products->add($initial);
        $found = $this->products->get($initial->getId());
        $this->assertSameProducts($initial, $found);
    }

    private function assertSameProducts(Product $initial, Product $other): void
    {
        $this->assertTrue($initial->getId()->equalsTo($other->getId()));
        $this->assertEquals($initial->getName(), $other->getName());
        $this->assertEquals($initial->getCode(), $other->getCode());
        $this->assertEquals($initial->isActive(), $other->isActive());
        $this->assertEquals($initial->getAvailability(), $other->getAvailability());
        $this->assertTrue($initial->samePrices($other->getPrices()));
        $this->assertTrue($initial->sameColors($other->getColors()));
        $this->assertSame($initial->getColors(), $other->getColors());
        $this->assertTrue($initial->sameSizes($other->getSizes()));
        $this->assertSame($initial->getSizes(), $other->getSizes());
        $this->assertSame(
            $initial->getCreatedAt()->getTimestamp(),
            $other->getCreatedAt()->getTimestamp()
        );
        $this->assertSame(
            $initial->getUpdatedAt()?->getTimestamp(),
            $other->getUpdatedAt()?->getTimestamp()
        );
    }

    public function testAddIncrementIds()
    {
        $product = $this->makeProduct(
            ProductId::next(),
            md5(rand()),
            md5(rand()),
            $this->makePrices()
        );
        $this->products->add($product);
        $this->assertNotNull($product->getId()->getId());
    }

    public function testAddWithDuplicatedId()
    {
        $product = $this->generateProduct();
        $productWithSameId = $this->makeProduct(
            $product->getId(),
            $product->getName(),
            'Unique product code',
            $this->makePrices()
        );
        $this->products->add($product);
        $this->expectException(DuplicateKeyException::class);
        $this->products->add($productWithSameId);
    }

    public function testAddWithNotUniqueCode()
    {
        $product = $this->generateProduct();
        $productWithNotUniqueCode = $this->generateProduct();
        $productWithNotUniqueCode->setCode($product->getCode());
        $this->products->add($product);
        $this->expectException(DuplicateKeyException::class);
        $this->products->add($productWithNotUniqueCode);
    }

    public function testUpdate()
    {
        $initial = $this->generateProduct();
        $this->products->add($initial);
        $added = $this->products->get($initial->getId());
        $added->setColors([
            md5(rand()),
            md5(rand()),
            md5(rand()),
        ]);
        $added->setCode(md5(rand()));
        $added->setName(md5(rand()));
        $this->products->update($added);
        $updated = $this->products->get($initial->getId());
        $this->assertSameProducts($added, $updated);
        $this->assertNotEquals($initial->getCode(), $updated->getCode());
        $this->assertNotEquals($initial->getName(), $updated->getName());
        $this->assertFalse($initial->sameColors($updated->getColors()));
    }

    public function testUpdateIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $product = $this->generateProduct();
        $this->products->update($product);
    }

    public function testUpdateWithNotUniqueCode()
    {
        $product = $this->generateProduct();
        $productWithNotUniqueCode = $this->generateProduct();
        $this->products->add($product);
        $this->products->add($productWithNotUniqueCode);
        $productWithNotUniqueCode->setCode($product->getCode());
        $this->expectException(DuplicateKeyException::class);
        $this->products->update($productWithNotUniqueCode);
    }

    public function testUpdateSameProductAndDoesNotChangeCode()
    {
        $product = $this->generateProduct();
        $this->products->add($product);
        $this->products->update($product);
        $this->expectNotToPerformAssertions();
    }

    public function testDelete()
    {
        $product = $this->generateProduct();
        $this->products->add($product);
        $this->products->delete($product);
        $this->expectException(NotFoundException::class);
        $this->products->get($product->getId());
    }

    public function testDeleteIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $product = $this->generateProduct();
        $this->products->delete($product);
    }

    public function testGet()
    {
        $product = $this->generateProduct();
        $this->products->add($product);
        $found = $this->products->get($product->getId());
        $this->assertSameProducts($product, $found);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->products->get(ProductId::random());
    }
}