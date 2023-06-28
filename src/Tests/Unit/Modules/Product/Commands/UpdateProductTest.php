<?php

namespace Project\Tests\Unit\Modules\Product\Commands;

use Project\Modules\Catalogue\Product\Entity;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Catalogue\Product\Api\DTO\Price;
use Project\Modules\Catalogue\Product\Entity\Product;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Product\Commands\UpdateProductCommand;
use Project\Modules\Catalogue\Product\Repository\MemoryProductRepository;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Catalogue\Product\Commands\Handlers\UpdateProductHandler;

class UpdateProductTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory;

    private ProductRepositoryInterface $products;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new MemoryProductRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->exactly(5)) // product updated, code changed, prices changed, activity changed, availability changed
        ->method('dispatch');
        parent::setUp();
    }

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $this->products->add($product);

        $command = new UpdateProductCommand(
            id: $product->getId()->getId(),
            name: md5(rand()),
            code: md5(rand()),
            active: false,
            availability: Availability::PREORDER->value,
            colors: [
                md5(rand()),
            ],
            sizes: [
                md5(rand()),
            ],
            prices: array_map(function (Entity\Price\Price $price) {
                return new Price(
                    $price->getCurrency()->value,
                    $price->getPrice(),
                );
            }, $this->makePrices())
        );
        $handler = new UpdateProductHandler($this->products);
        $handler->setDispatcher($this->dispatcher);

        call_user_func($handler, $command);
        $product = $this->products->get($product->getId());
        $this->assertSameProduct($product, $command);
    }

    private function assertSameProduct(Product $product, UpdateProductCommand $command): void
    {
        $this->assertTrue($product->getId()->equalsTo(new ProductId($command->id)));
        $this->assertSame($command->name, $product->getName());
        $this->assertSame($command->code, $product->getCode());
        $this->assertSame($command->availability, $product->getAvailability()->value);
        $this->assertSame($command->active, $product->isActive());

        $this->assertCount(count($command->colors), $product->getColors());
        foreach ($command->colors as $color) {
            $this->assertTrue(in_array($color, $product->getColors()));
        }

        $this->assertCount(count($command->sizes), $product->getSizes());
        foreach ($command->sizes as $size) {
            $this->assertTrue(in_array($size, $product->getSizes()));
        }

        $this->assertCount(count($command->prices), $product->getPrices());
        foreach ($command->prices as $price) {
            $this->assertTrue((new Entity\Price\Price(
                Currency::from($price->currency),
                $price->price,
            ))->equalsTo($product->getPrices()[$price->currency]));
        }
    }
}