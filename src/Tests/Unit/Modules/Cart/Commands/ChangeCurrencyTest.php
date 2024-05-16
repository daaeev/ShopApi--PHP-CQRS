<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Adapters\CatalogueService;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Commands\ChangeCurrencyCommand;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\ChangeCurrencyHandler;

class ChangeCurrencyTest extends \PHPUnit\Framework\TestCase
{
    private Client $client;
    private Cart $cart;

    private Offer $offer;
    private OfferId $offerId;
    private int $product = 1;
    private int $quantity = 1;
    private string $size = 'size';
    private string $color = 'color';

    private CartsRepositoryInterface $carts;
    private CatalogueService $catalogue;
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

        $this->offerId = OfferId::random();
        $this->offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->catalogue = $this->getMockBuilder(CatalogueService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
    }

    public function testChangeCurrency()
    {
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->carts->expects($this->once())
            ->method('getByClient')
            ->with($this->client)
            ->willReturn($this->cart);

        $command = new ChangeCurrencyCommand($currency = Currency::default()->value);

        $this->cart->expects($this->once())
            ->method('changeCurrency')
            ->with(Currency::default());

        $this->cart->expects($this->once())
            ->method('getOffers')
            ->willReturn([$this->offer, $this->offer]);

        $this->mockOfferMethods();
        $this->cart->expects($this->exactly(2))
            ->method('getCurrency')
            ->willReturn(Currency::default());

        $this->catalogue->expects($this->exactly(2))
            ->method('resolveOffer')
            ->with($this->product, $this->quantity, Currency::default(), $this->size, $this->color)
            ->willReturn($this->offer);

        $this->cart->expects($this->exactly(2))
            ->method('removeOffer')
            ->with($this->offerId);

        $this->cart->expects($this->exactly(2))
            ->method('addOffer')
            ->with($this->offer);

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with($this->cart);

        $this->carts->expects($this->once())
            ->method('save')
            ->with($this->cart);

        $this->cart->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$event = new CartUpdated($this->cart)]);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($event);

		$handler = new ChangeCurrencyHandler($this->carts, $this->catalogue, $this->discountsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }

    private function mockOfferMethods(): void
    {
        $this->offer->expects($this->exactly(2))
            ->method('getProduct')
            ->willReturn($this->product);

        $this->offer->expects($this->exactly(2))
            ->method('getQuantity')
            ->willReturn($this->quantity);

        $this->offer->expects($this->exactly(2))
            ->method('getSize')
            ->willReturn($this->size);

        $this->offer->expects($this->exactly(2))
            ->method('getColor')
            ->willReturn($this->color);

        $this->offer->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->offerId);
    }
}