<?php

namespace Project\Tests\Unit\Modules\Product\Commands;

use Project\Common\Currency;
use Project\Modules\Product\Api\DTO\Price;
use Project\Modules\Product\Entity;
use Project\Modules\Product\Entity\Product;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Product\Entity\ProductId;
use Project\Modules\Product\Entity\Availability;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Product\Commands\CreateProductCommand;
use Project\Modules\Product\Repository\MemoryProductRepository;
use Project\Modules\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Product\Commands\Handlers\CreateProductHandler;

class CreateProductTest extends \PHPUnit\Framework\TestCase
{
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
            prices: [
                new Price(
                    Currency::default()->value,
                    rand()
                ),
            ]
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
        $this->assertCount(1, $product->getColors());
        $this->assertTrue(in_array($command->colors[0], $product->getColors()));
        $this->assertCount(1, $product->getSizes());
        $this->assertTrue(in_array($command->sizes[0], $product->getSizes()));
        $this->assertCount(1, $product->getPrices());
        $this->assertTrue((new Entity\Price\Price(
            Currency::from($command->prices[0]->currency),
            $command->prices[0]->price,
        ))->equalsTo($product->getPrices()[$command->prices[0]->currency]));
    }
}