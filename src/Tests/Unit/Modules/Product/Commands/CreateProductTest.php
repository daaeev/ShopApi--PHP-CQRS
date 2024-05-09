<?php

namespace Project\Tests\Unit\Modules\Product\Commands;

use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Catalogue\Product\Entity;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Catalogue\Product\Commands\CreateProductCommand;
use Project\Modules\Catalogue\Product\Repository\ProductsMemoryRepository;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;
use Project\Modules\Catalogue\Product\Commands\Handlers\CreateProductHandler;

class CreateProductTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory;

    private ProductsRepositoryInterface $products;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new ProductsMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(2)) // product created, product updated
            ->method('dispatch');
    }

    public function testCreate()
    {
        $command = new CreateProductCommand(
            name: md5(rand()),
            code: md5(rand()),
            active: true,
            availability: Availability::IN_STOCK->value,
            colors: [md5(rand())],
            sizes: [md5(rand())],
            prices: array_map(function (Entity\Price\Price $price) {
                return new DTO\Price($price->getCurrency()->value, $price->getPrice());
            }, $this->makePrices())
        );

        $handler = new CreateProductHandler($this->products);
        $handler->setDispatcher($this->dispatcher);
        $productId = call_user_func($handler, $command);

        $product = $this->products->get(new Entity\ProductId($productId));
        $this->assertSameProduct($product, $command);
    }

    private function assertSameProduct(Entity\Product $product, CreateProductCommand $command): void
    {
        $this->assertSame($command->name, $product->getName());
        $this->assertSame($command->code, $product->getCode());
        $this->assertSame($command->availability, $product->getAvailability()->value);
        $this->assertSame($command->active, $product->isActive());
        $this->assertSame($command->colors, $product->getColors());
        $this->assertSame($command->sizes, $product->getSizes());

        $this->assertCount(count($command->prices), $product->getPrices());
        foreach ($command->prices as $price) {
            $priceEntity = new Entity\Price\Price(Currency::from($price->currency), $price->price);
            $this->assertTrue($priceEntity->equalsTo($product->getPrices()[$price->currency]));
        }
    }
}
