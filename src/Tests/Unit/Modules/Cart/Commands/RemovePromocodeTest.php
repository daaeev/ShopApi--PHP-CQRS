<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Commands\RemovePromocodeCommand;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeRemovedFromCart;
use Project\Modules\Shopping\Cart\Commands\Handlers\RemovePromocodeHandler;

class RemovePromocodeTest extends \PHPUnit\Framework\TestCase
{
    private Client $client;
    private Cart $cart;
    private Offer $offer;

    private CartsRepositoryInterface $carts;
    private DiscountsService $discountsService;
    private EnvironmentInterface $environment;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();

        $this->carts = $this->getMockBuilder(CartsRepositoryInterface::class)->getMock();
        $this->cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
    }

    public function testRemovePromocode()
    {
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->carts->expects($this->once())
            ->method('getByClient')
            ->with($this->client)
            ->willReturn($this->cart);

        $this->cart->expects($this->once())->method('removePromocode');

        $this->cart->expects($this->once())
            ->method('getOffers')
            ->willReturn([$this->offer]);

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with([$this->offer])
            ->willReturn([$this->offer]);

        $this->cart->expects($this->once())
            ->method('setOffers')
            ->with([$this->offer]);

        $this->carts->expects($this->once())
            ->method('save')
            ->with($this->cart);

        $this->cart->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$event = new PromocodeRemovedFromCart($this->cart)]);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($event);

        $command = new RemovePromocodeCommand();
		$handler = new RemovePromocodeHandler($this->carts, $this->discountsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }
}