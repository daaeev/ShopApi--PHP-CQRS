<?php

namespace Project\Tests\Unit\Modules\Product\Commands;

use Project\Common\Currency;
use Project\Modules\Product\Api\DTO\Color;
use Project\Modules\Product\Api\DTO\Price;
use Project\Modules\Product\Entity\Product;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Product\Entity\ProductId;
use Project\Modules\Product\Entity;
use Project\Modules\Product\Entity\Size\Size;
use Project\Modules\Product\Entity\Availability;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Product\Entity\Color\HexColor;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Commands\UpdateProductCommand;
use Project\Modules\Product\Repository\MemoryProductRepository;
use Project\Modules\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Product\Commands\Handlers\UpdateProductHandler;

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
                new Color(
                    md5(rand()),
                    md5(rand()),
                    'hex'
                ),
            ],
            sizes: [
                md5(rand()),
            ],
            prices: [
                new Price(
                    Currency::default()->value,
                    $product->getPrices()[Currency::default()->value]->getPrice() + rand(1, 100)
                ),
            ]
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
        $this->assertCount(1, $product->getColors());
        $this->assertTrue((new HexColor(md5(rand()), $command->colors[0]->color))->equalsTo($product->getColors()[$command->colors[0]->color]));
        $this->assertCount(1, $product->getSizes());
        $this->assertTrue((new Size($command->sizes[0]))->equalsTo($product->getSizes()[$command->sizes[0]]));
        $this->assertCount(1, $product->getPrices());
        $this->assertTrue((new Entity\Price\Price(
            Currency::from($command->prices[0]->currency),
            $command->prices[0]->price,
        ))->equalsTo($product->getPrices()[$command->prices[0]->currency]));
    }
}