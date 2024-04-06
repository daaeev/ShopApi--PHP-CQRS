<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Entity\CartItemBuilder;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Modules\Shopping\Cart\Commands\UpdateItemCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\UpdateItemHandler;

class UpdateItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    private CartsRepositoryInterface $carts;
    private EnvironmentInterface $environment;
    private Client $client;
    private MessageBusInterface $dispatcher;
    private DiscountsService $discountsService;
    private CartItemBuilder $builderMock;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = $this->getMockBuilder(CartsRepositoryInterface::class)->getMock();
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $this->builderMock = $this->getMockBuilder(CartItemBuilder::class)->getMock();
        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        parent::setUp();
    }

    public function testUpdateCartItem()
    {
        $cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->carts->expects($this->once())
            ->method('getActiveCart')
            ->with($this->client)
            ->willReturn($cart);

        $cartItem = $this->generateCartItem();
        $cart->expects($this->once())
            ->method('getItem')
            ->with(self::callback(fn ($id) => $id->equalsTo($cartItem->getId())))
            ->willReturn($cartItem);

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with($cart);

        $this->builderMock->expects($this->once())
            ->method('from')
            ->with($cartItem)
            ->willReturnSelf();

        $quantityToUpdate = $cartItem->getQuantity() + 1;
        $this->builderMock->expects($this->once())
            ->method('withQuantity')
            ->with($quantityToUpdate)
            ->willReturnSelf();

        $this->builderMock->expects($this->once())
            ->method('build')
            ->willReturn($buildedCartItem = $this->generateCartItem());

        $cart->expects($this->once())
            ->method('addItem')
            ->with($buildedCartItem);

        $cart->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$event = new CartUpdated($cart)]);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($event);

		$command = new UpdateItemCommand($cartItem->getId()->getId(), $quantityToUpdate);
        $handler = new UpdateItemHandler($this->carts, $this->discountsService, $this->environment, $this->builderMock);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }
}