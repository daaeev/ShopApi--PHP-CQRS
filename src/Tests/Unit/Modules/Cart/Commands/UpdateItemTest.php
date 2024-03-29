<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Adapters\ProductsService;
use Project\Modules\Shopping\Cart\Commands\UpdateItemCommand;
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
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = new CartsMemoryRepository(new Hydrator);

        $this->productsService = $this->getMockBuilder(ProductsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)
            ->getMock();
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();

        parent::setUp();
    }

    public function testUpdateCartItem()
    {
        $this->dispatcher->expects($this->once()) // Cart updated
            ->method('dispatch');

        $initialCart = $this->makeCart(
            CartId::next(),
            $this->client,
            [$initialCartItem = $this->generateCartItem()]
        );
        $initialCart->flushEvents();
        $this->carts->save($initialCart);

        $command = new UpdateItemCommand(
            $initialCartItem->getId()->getId(),
            $initialCartItem->getQuantity() + 1,
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
            ->willReturn($updatedCartItem = $this->makeCartItem(
                CartItemId::next(),
                $initialCartItem->getProduct(),
                $initialCartItem->getName(),
                $initialCartItem->getPrice(),
                $initialCartItem->getQuantity() + 1,
                $initialCartItem->getSize(),
                $initialCartItem->getColor(),
            ));
        $handler = new UpdateItemHandler(
            $this->carts,
            $this->productsService,
            $this->environment
        );
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $cart = $this->carts->getActiveCart($this->client);
        $this->assertSame($cart->getClient(), $this->client);
        $this->assertCount(1, $cart->getItems());

        $this->assertTrue($updatedCartItem->equalsTo($cart->getItems()[0]));
        $this->assertTrue($updatedCartItem->getId()->equalsTo($cart->getItems()[0]->getId()));
        $this->assertSame($updatedCartItem->getQuantity(), $cart->getItems()[0]->getQuantity());

        $this->assertTrue($initialCartItem->equalsTo($cart->getItems()[0]));
        $this->assertFalse($initialCartItem->getId()->equalsTo($cart->getItems()[0]->getId()));
        $this->assertNotSame($initialCartItem->getQuantity(), $cart->getItems()[0]->getQuantity());
    }

    public function testUpdateCartItemIfDoesNotExists()
    {
        $initialCart = $this->makeCart(
            CartId::next(),
            $this->client,
        );
        $initialCart->flushEvents();
        $this->carts->save($initialCart);

        $command = new UpdateItemCommand(
            1,
            1,
        );
        $handler = new UpdateItemHandler(
            $this->carts,
            $this->productsService,
            $this->environment
        );
        $handler->setDispatcher($this->dispatcher);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}