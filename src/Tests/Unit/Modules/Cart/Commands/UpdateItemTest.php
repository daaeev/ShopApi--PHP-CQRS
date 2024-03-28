<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
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

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = new CartsMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
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
        // Cart updated
        $this->dispatcher->expects($this->once())->method('dispatch');

        $cart = $this->makeCart(CartId::next(), $this->client);
		$cartItem = $this->generateCartItem();
        $cart->addItem($cartItem);
        $cart->flushEvents();
        $this->carts->save($cart);

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with($cart);

        $quantityToUpdate = $cartItem->getQuantity() + 1;
		$command = new UpdateItemCommand($cartItem->getId()->getId(), $quantityToUpdate);
        $handler = new UpdateItemHandler($this->carts, $this->discountsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $updatedCartItem = $cart->getItem($cartItem->getId());
        $this->assertSame($cartItem, $updatedCartItem);
        $this->assertSame($updatedCartItem->getQuantity(), $quantityToUpdate);
    }
}