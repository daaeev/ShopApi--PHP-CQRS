<?php

namespace Project\Tests\Unit\Modules\Product\Commands;

use Project\Modules\Product\Entity;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Product\Api\DTO\Price;
use Project\Modules\Product\Entity\Product;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Product\Entity\ProductId;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Commands\CreateProductCommand;
use Project\Modules\Product\Repository\MemoryProductRepository;
use Project\Modules\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Product\Commands\Handlers\CreateProductHandler;

class CreateProductTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory;

    private ProductRepositoryInterface $products;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new MemoryProductRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->exactly(2)) // product created, product updated
            ->method('dispatch');
        parent::setUp();
    }

    public function testCreate()
    {
        $command = new CreateProductCommand(
            name: md5(rand()),
            code: md5(rand()),
            active: true,
            availability: Availability::IN_STOCK->value,
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
        $handler = new CreateProductHandler($this->products);
        $handler->setDispatcher($this->dispatcher);

        $productId = call_user_func($handler, $command);
        $product = $this->products->get(new ProductId($productId));
        $this->assertSameProduct($product, $command);
    }

    private function assertSameProduct(Product $product, CreateProductCommand $command): void
    {
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