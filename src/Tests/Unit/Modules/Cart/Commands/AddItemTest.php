<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Adapters\CatalogueService;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Modules\Shopping\Cart\Commands\AddOfferCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\AddOfferHandler;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class AddItemTest extends \PHPUnit\Framework\TestCase
{
    private Client $client;
    private Cart $cart;
    private Offer $offer;

    private CartsRepositoryInterface $carts;
    private CatalogueService $productsService;
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

        $this->productsService = $this->getMockBuilder(CatalogueService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
    }

    public function testAddItem()
    {
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->carts->expects($this->once())
            ->method('getByClient')
            ->with($this->client)
            ->willReturn($this->cart);

        $command = new AddOfferCommand(
            $product = rand(1, 10),
            $quantity = rand(1, 10),
            $size = md5(rand()),
            $color = md5(rand()),
        );

        $this->cart->expects($this->once())
            ->method('getCurrency')
            ->willReturn(Currency::default());

        $this->productsService->expects($this->once())
            ->method('resolveOffer')
            ->with($product, $quantity, Currency::default(), $size, $color)
            ->willReturn($this->offer);

        $this->cart->expects($this->once())
            ->method('addOffer')
            ->with($this->offer);

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
            ->willReturn([$event = new CartUpdated($this->cart)]);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($event);

		$handler = new AddOfferHandler($this->carts, $this->productsService, $this->discountsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }
}