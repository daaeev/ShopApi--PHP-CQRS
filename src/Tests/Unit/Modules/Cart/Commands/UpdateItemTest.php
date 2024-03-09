<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Adapters\ProductsService;
use Project\Modules\Shopping\Cart\Commands\UpdateItemCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\UpdateItemHandler;

class UpdateItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    private CartsRepositoryInterface $carts;
    private ProductsService $productsService;
    private EnvironmentInterface $environment;
    private Client $client;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = new CartsMemoryRepository(new Hydrator, new IdentityMap);

        $this->productsService = $this->getMockBuilder(ProductsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)
            ->getMock();

        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        parent::setUp();
    }

    public function testUpdateCartItem()
    {
        $this->dispatcher->expects($this->once()) // Cart updated
            ->method('dispatch');

        $initialCart = $this->makeCart(CartId::next(), $this->client,);
		$initialCartItem = $this->generateCartItem();
		$initialCart->addItem($initialCartItem);
        $initialCart->flushEvents();
        $this->carts->save($initialCart);

		$updatedCartItem = $this->makeCartItem(
			CartItemId::next(),
			$initialCartItem->getProduct(),
			$initialCartItem->getName(),
			$initialCartItem->getPrice(),
			$initialCartItem->getQuantity() + 1,
			$initialCartItem->getSize(),
			$initialCartItem->getColor(),
		);

        $this->productsService->expects($this->once())
            ->method('resolveCartItem')
            ->with(
                $initialCartItem->getProduct(),
                $initialCartItem->getQuantity() + 1,
                $initialCart->getCurrency(),
                $initialCartItem->getSize(),
                $initialCartItem->getColor(),
                false
            )
            ->willReturn($updatedCartItem);

		$command = new UpdateItemCommand(
			$initialCartItem->getId()->getId(),
			$initialCartItem->getQuantity() + 1,
		);

        $handler = new UpdateItemHandler($this->carts, $this->productsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->assertCount(1, $initialCart->getItems());

        $this->assertTrue($updatedCartItem->equalsTo($initialCart->getItems()[0]));
        $this->assertTrue($updatedCartItem->getId()->equalsTo($initialCart->getItems()[0]->getId()));
        $this->assertSame($updatedCartItem->getQuantity(), $initialCart->getItems()[0]->getQuantity());

        $this->assertTrue($initialCartItem->equalsTo($initialCart->getItems()[0]));
    }

    public function testUpdateCartItemIfDoesNotExists()
    {
        $initialCart = $this->makeCart(CartId::next(), $this->client);
        $initialCart->flushEvents();
        $this->carts->save($initialCart);

        $command = new UpdateItemCommand(1, 1);
        $handler = new UpdateItemHandler($this->carts, $this->productsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);

        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}