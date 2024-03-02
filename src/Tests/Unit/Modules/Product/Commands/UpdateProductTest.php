<?php

namespace Project\Tests\Unit\Modules\Product\Commands;

use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Catalogue\Product\Entity;
use Project\Modules\Catalogue\Api\DTO\Product\Price;
use Project\Modules\Catalogue\Product\Entity\Product;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Catalogue\Product\Commands\UpdateProductCommand;
use Project\Modules\Catalogue\Product\Repository\ProductsMemoryRepository;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;
use Project\Modules\Catalogue\Product\Commands\Handlers\UpdateProductHandler;

class UpdateProductTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory;

    private ProductsRepositoryInterface $products;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new ProductsMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(5)) // product updated, code changed, prices changed, activity changed, availability changed
            ->method('dispatch');

        parent::setUp();
    }

    public function testUpdate()
    {
        $initial = $this->generateProduct();
        $this->products->add($initial);

        $command = new UpdateProductCommand(
            id: $initial->getId()->getId(),
            name: md5(rand()),
            code: md5(rand()),
            active: false,
            availability: Availability::PREORDER->value,
            colors: [md5(rand())],
            sizes: [md5(rand())],
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

        $updated = $this->products->get($initial->getId());
        $this->assertSameProduct($updated, $command);
    }

    private function assertSameProduct(Product $product, UpdateProductCommand $command): void
    {
        $this->assertTrue($product->getId()->equalsTo(new ProductId($command->id)));
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
