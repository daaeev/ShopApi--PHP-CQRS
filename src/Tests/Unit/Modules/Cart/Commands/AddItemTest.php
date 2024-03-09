<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Product\Currency;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Commands\AddItemCommand;
use Project\Modules\Shopping\Cart\Adapters\ProductsService;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
use Project\Modules\Shopping\Cart\Commands\Handlers\AddItemHandler;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class AddItemTest extends \PHPUnit\Framework\TestCase
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

    public function testAddItemIfCartDoesNotInstantiated()
    {
        $this->dispatcher->expects($this->exactly(2)) // Cart instantiated, cart updated
            ->method('dispatch');

        $command = new AddItemCommand(
            $product = rand(1, 10),
            $quantity = rand(1, 10),
            $size = md5(rand()),
            $color = md5(rand()),
        );

        $this->productsService->expects($this->once())
            ->method('resolveCartItem')
            ->with($product, $quantity, Currency::default(), $size, $color, true)
            ->willReturn($cartItem = $this->generateCartItem());

		$handler = new AddItemHandler(
            $this->carts,
            $this->productsService,
            $this->environment
        );

        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $cart = $this->carts->getActiveCart($this->client);
        $this->assertSame($cart->getClient(), $this->client);
        $this->assertCount(1, $cart->getItems());
        $this->assertTrue($cartItem->equalsTo($cart->getItems()[0]));
        $this->assertTrue($cartItem->getId()->equalsTo($cart->getItems()[0]->getId()));
        $this->assertSame($cartItem->getQuantity(), $cart->getItems()[0]->getQuantity());
    }

    public function testAddItemIfCartInstantiatedBefore()
    {
        $this->dispatcher->expects($this->once())->method('dispatch'); // Cart updated
        $initialCart = $this->makeCart(CartId::next(), $this->client);
		$initialCartItem = $this->generateCartItem();
		$initialCart->addItem($initialCartItem);
        $initialCart->flushEvents();
        $this->carts->save($initialCart);

        $command = new AddItemCommand(
            $product = $initialCartItem->getProduct() + 1,
            $quantity = rand(1, 10),
            $size = md5(rand()),
            $color = md5(rand()),
        );

        $this->productsService->expects($this->once())
            ->method('resolveCartItem')
            ->with($product, $quantity, $initialCart->getCurrency(), $size, $color, true)
            ->willReturn($addedCartItem = $this->generateCartItem());

		$handler = new AddItemHandler($this->carts, $this->productsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->assertCount(2, $initialCart->getItems());
        $this->assertTrue($initialCartItem->equalsTo($initialCart->getItems()[0]));
        $this->assertTrue($addedCartItem->equalsTo($initialCart->getItems()[1]));
    }
}