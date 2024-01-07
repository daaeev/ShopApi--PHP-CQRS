<?php

namespace Project\Tests\Unit\Modules\Product\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\CQRS\Buses\MessageBusInterface;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Product\Commands\DeleteProductCommand;
use Project\Modules\Catalogue\Product\Repository\ProductsMemoryRepository;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;
use Project\Modules\Catalogue\Product\Commands\Handlers\DeleteProductHandler;

class DeleteProductTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory;

    private ProductsRepositoryInterface $products;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new ProductsMemoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        parent::setUp();
    }

    public function testDelete()
    {
        $this->dispatcher->expects($this->exactly(1)) // product deleted
            ->method('dispatch');

        $product = $this->generateProduct();
        $product->deactivate();
        $product->flushEvents();
        $this->products->add($product);

        $command = new DeleteProductCommand(
            id: $product->getId()->getId(),
        );
        $handler = new DeleteProductHandler($this->products);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
        $this->expectException(NotFoundException::class);
        $this->products->get($product->getId());
    }

    public function testDeleteActiveProduct()
    {
        $product = $this->generateProduct();
        $product->deactivate();
        $product->flushEvents();
        $this->products->add($product);

        $command = new DeleteProductCommand(
            id: $product->getId()->getId(),
        );
        $handler = new DeleteProductHandler($this->products);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}