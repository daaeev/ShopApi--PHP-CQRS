<?php

namespace Project\Tests\Unit\Modules\Product\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Commands\DeleteProductCommand;
use Project\Modules\Product\Repository\MemoryProductRepository;
use Project\Modules\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Product\Commands\Handlers\DeleteProductHandler;

class DeleteProductTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory;

    private ProductRepositoryInterface $products;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new MemoryProductRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->exactly(1)) // product deleted
        ->method('dispatch');
        parent::setUp();
    }

    public function testDelete()
    {
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
}