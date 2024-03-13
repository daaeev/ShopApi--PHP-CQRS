<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Adapters\CatalogueService;
use Project\Modules\Shopping\Cart\Commands\RemoveItemCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\RemoveItemHandler;

class RemoveItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    private CartsRepositoryInterface $carts;
    private CatalogueService $productsService;
    private EnvironmentInterface $environment;
    private Client $client;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = new CartsMemoryRepository(new Hydrator, new IdentityMap);

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)
            ->getMock();

        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        parent::setUp();
    }

	public function testRemoveCartItem()
	{
		$this->dispatcher->expects($this->exactly(1)) // Cart updated
			->method('dispatch');

		$initialCart = $this->makeCart(CartId::next(), $this->client);
		$initialCart->addItem($cartItem = $this->generateCartItem());
		$initialCart->flushEvents();
		$this->carts->save($initialCart);

		$command = new RemoveItemCommand($cartItem->getId()->getId());
		$handler = new RemoveItemHandler($this->carts, $this->environment);
		$handler->setDispatcher($this->dispatcher);
		call_user_func($handler, $command);

		$this->assertEmpty($initialCart->getItems());
	}

    public function testRemoveCartItemIfDoesNotExists()
    {
        $initialCart = $this->makeCart(CartId::next(), $this->client);
        $initialCart->flushEvents();
        $this->carts->save($initialCart);

        $command = new RemoveItemCommand(1);
        $handler = new RemoveItemHandler($this->carts, $this->environment);
        $handler->setDispatcher($this->dispatcher);

        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}