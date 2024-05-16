<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\UsePromocodeCommand;
use Project\Modules\Shopping\Entity\Promocode as CartPromocode;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeAddedToCart;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\Promocode;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\UsePromocodeHandler;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class UsePromocodeTest extends \PHPUnit\Framework\TestCase
{
    private Client $client;
    private Cart $cart;
    private Promocode $promocode;

    private CartsRepositoryInterface $carts;
    private PromocodesRepositoryInterface $promocodes;
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

        $this->promocodes = $this->getMockBuilder(PromocodesRepositoryInterface::class)->getMock();
        $this->promocode = $this->getMockBuilder(Promocode::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
    }

    public function testUsePromocode()
    {
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->carts->expects($this->once())
            ->method('getByClient')
            ->with($this->client)
            ->willReturn($this->cart);

        $command = new UsePromocodeCommand($promo = 'promo');

        $this->promocodes->expects($this->once())
            ->method('getByCode')
            ->with($promo)
            ->willReturn($this->promocode);

        $this->promocode->expects($this->once())
            ->method('isActive')
            ->willReturn(true);

        $cartPromocode = new CartPromocode(
            PromocodeId::random(),
            'promo',
            15
        );

        $this->promocode->expects($this->once())
            ->method('getId')
            ->willReturn($cartPromocode->getId());

        $this->promocode->expects($this->once())
            ->method('getCode')
            ->willReturn($cartPromocode->getCode());

        $this->promocode->expects($this->once())
            ->method('getDiscountPercent')
            ->willReturn($cartPromocode->getDiscountPercent());

        $this->cart->expects($this->once())
            ->method('usePromocode')
            ->with($cartPromocode);

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with($this->cart);

        $this->carts->expects($this->once())
            ->method('save')
            ->with($this->cart);

        $this->cart->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$event = new PromocodeAddedToCart($this->cart)]);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($event);

		$handler = new UsePromocodeHandler($this->carts, $this->environment, $this->promocodes, $this->discountsService);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }

    public function testUseInactivePromocode()
    {
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->carts->expects($this->once())
            ->method('getByClient')
            ->with($this->client)
            ->willReturn($this->cart);

        $command = new UsePromocodeCommand($promo = 'promo');

        $this->promocodes->expects($this->once())
            ->method('getByCode')
            ->with($promo)
            ->willReturn($this->promocode);

        $this->expectException(\DomainException::class);

        $this->promocode->expects($this->once())
            ->method('isActive')
            ->willReturn(false);

        $handler = new UsePromocodeHandler($this->carts, $this->environment, $this->promocodes, $this->discountsService);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }
}