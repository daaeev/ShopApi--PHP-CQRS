<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Common\Services\Environment\Client;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Commands\UpdateOfferCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\UpdateOfferHandler;

class UpdateItemTest extends \PHPUnit\Framework\TestCase
{
    private Client $client;
    private Cart $cart;
    private Offer $offer;

    private CartsRepositoryInterface $carts;
    private DiscountsService $discountsService;
    private EnvironmentInterface $environment;
    private OfferBuilder $offerBuilder;
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

        $this->offerBuilder = $this->getMockBuilder(OfferBuilder::class)->getMock();

        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
    }

    public function testUpdateItem()
    {
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->carts->expects($this->once())
            ->method('getByClient')
            ->with($this->client)
            ->willReturn($this->cart);

        $command = new UpdateOfferCommand($product = rand(1, 10), $quantity = rand(1, 10));

        $this->cart->expects($this->once())
            ->method('getOffer')
            ->with(OfferId::make($product))
            ->willReturn($this->offer);

        $this->offerBuilder->expects($this->once())
            ->method('from')
            ->with($this->offer)
            ->willReturnSelf();

        $this->offerBuilder->expects($this->once())
            ->method('withQuantity')
            ->with($quantity)
            ->willReturnSelf();

        $this->offerBuilder->expects($this->once())
            ->method('build')
            ->willReturn($this->offer);

        $this->cart->expects($this->once())
            ->method('replaceOffer')
            ->with($this->offer, $this->offer);

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

		$handler = new UpdateOfferHandler($this->carts, $this->discountsService, $this->environment, $this->offerBuilder);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }
}