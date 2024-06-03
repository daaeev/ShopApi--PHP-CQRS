<?php

namespace Project\Tests\Unit\Modules\Product\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
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
        $id = $initial->getId();
        $name = $initial->getName();
        $code = $initial->getCode();
        $active = $initial->isActive();
        $availability = $initial->getAvailability();
        $prices = $initial->getPrices();
        $initial->setColors($colors = [uniqid(), uniqid()]);
        $initial->setSizes($sizes = [uniqid()]);
        $createdAt = $initial->getCreatedAt();
        $updatedAt = $initial->getUpdatedAt();

        $this->products->add($initial);

        $found = $this->products->get($initial->getId());
        $this->assertSame($initial, $found);
        $this->assertTrue($found->getId()->equalsTo($id));
        $this->assertSame($found->getName(), $name);
        $this->assertSame($found->getCode(), $code);
        $this->assertSame($found->isActive(), $active);
        $this->assertSame($found->getAvailability(), $availability);
        $this->assertSame($found->getPrices(), $prices);
        $this->assertSame($found->getColors(), $colors);
        $this->assertSame($found->getSizes(), $sizes);
        $this->assertSame($found->getCreatedAt()->getTimestamp(), $createdAt->getTimestamp());
        $this->assertSame($found->getUpdatedAt()?->getTimestamp(), $updatedAt?->getTimestamp());
    }

    public function testAddIncrementIds()
    {
        $product = $this->makeProduct(
            ProductId::next(),
            uniqid(),
            uniqid(),
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
        $added->setName($name = uniqid());
        $added->setCode($code = uniqid());
        $added->setSizes($sizes = [uniqid()]);
        $added->setColors($colors = [uniqid(), md5(rand()), md5(rand())]);
        $active = $added->isActive();
        $availability = $added->getAvailability();
        $prices = $added->getPrices();
        $createdAt = $added->getCreatedAt();
        $updatedAt = $added->getUpdatedAt();
        $this->products->update($added);

        $updated = $this->products->get($initial->getId());
        $this->assertSame($initial, $added);
        $this->assertSame($added, $updated);
        $this->assertSame($updated->getName(), $name);
        $this->assertSame($updated->getCode(), $code);
        $this->assertSame($updated->getSizes(), $sizes);
        $this->assertSame($updated->getColors(), $colors);
        $this->assertSame($updated->isActive(), $active);
        $this->assertSame($updated->getAvailability(), $availability);
        $this->assertSame($updated->getPrices(), $prices);
        $this->assertSame($updated->getCreatedAt()->getTimestamp(), $createdAt->getTimestamp());
        $this->assertSame($updated->getUpdatedAt()?->getTimestamp(), $updatedAt?->getTimestamp());
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

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->products->get(ProductId::random());
    }
}
