<?php

namespace Project\Tests\Unit\Modules\Product\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Catalogue\Product\Entity\Product;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;

trait ProductRepositoryTestTrait
{
    use ProductFactory;

    protected ProductRepositoryInterface $products;

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

    private function assertSameProducts(Product $initial, Product $found): void
    {
        $this->assertTrue($initial->getId()->equalsTo($found->getId()));
        $this->assertEquals($initial->getName(), $found->getName());
        $this->assertEquals($initial->getCode(), $found->getCode());
        $this->assertEquals($initial->isActive(), $found->isActive());
        $this->assertEquals($initial->getAvailability(), $found->getAvailability());
        $this->assertTrue($initial->samePrices($found->getPrices()));
        $this->assertTrue($initial->sameColors($found->getColors()));
        $this->assertSame($initial->getColors(), $found->getColors());
        $this->assertTrue($initial->sameSizes($found->getSizes()));
        $this->assertSame($initial->getSizes(), $found->getSizes());
    }

    public function testAddIncrementIds()
    {
        $product = $this->makeProduct(
            ProductId::next(),
            md5(rand()),
            md5(rand()),
            $this->makePrices()
        );

        $this->assertNull($product->getId()->getId());
        $this->products->add($product);
        $this->assertNotNull($product->getId()->getId());
    }

    public function testAddIfAlreadyExists()
    {
        $product = $this->generateProduct();
        $secondProduct = $this->makeProduct(
            $product->getId(),
            $product->getName(),
            'Unique product code',
            $this->makePrices()
        );
        $this->products->add($product);
        $this->expectException(DuplicateKeyException::class);
        $this->products->add($secondProduct);
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