<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Cart\Entity\CartItem;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Entity\CartItemId;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Adapters\CatalogueService;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Commands\ChangeCurrencyCommand;
use Project\Modules\Shopping\Api\Events\Cart\CartCurrencyChanged;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\ChangeCurrencyHandler;

// Test disabled because cant mock Currency enum for test currency
class ChangeCurrencyTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    private CartsRepositoryInterface $carts;
    private CatalogueService $productsService;
    private EnvironmentInterface $environment;
    private Client $client;
    private MessageBusInterface $dispatcher;
    private DiscountsService $discountsService;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = $this->getMockBuilder(CartsRepositoryInterface::class)->getMock();
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productsService = $this->getMockBuilder(CatalogueService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        parent::setUp();
    }

    public function testChangeCartCurrency()
    {
        $currencyToUpdate = Currency::default();
        $oldCartItem = $this->getCartItemMock();
        $newCartItem = $this->generateCartItem();

        $cart = $this->getCartMock($currencyToUpdate, $oldCartItem, $newCartItem);
        $cart->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$event = new CartCurrencyChanged($cart)]);

        $this->dispatcher->expects($this->exactly(1))
            ->method('dispatch')
            ->with($event);

        $this->registerCatalogueServiceMock($currencyToUpdate, $newCartItem);

        $this->carts->expects($this->once())
            ->method('getActiveCart')
            ->with($this->client)
            ->willReturn($cart);

        $this->carts->expects($this->once())
            ->method('save')
            ->with($cart);

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with($cart);

        $command = new ChangeCurrencyCommand($currencyToUpdate->value);
        $handler = new ChangeCurrencyHandler($this->carts, $this->productsService, $this->discountsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
    }

    private function getCartMock(Currency $currency, CartItem $oldCartItem, CartItem $newCartItem): Cart
    {
        $cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cart->expects($this->once())
            ->method('changeCurrency')
            ->with($currency);

        $cart->expects($this->once())
            ->method('getItems')
            ->willReturn([$oldCartItem]);

        $cart->expects($this->once())
            ->method('removeItem')
            ->with(CartItemId::make(1));

        $cart->expects($this->once())
            ->method('addItem')
            ->with($newCartItem);

        $cart->expects($this->once())
            ->method('getCurrency')
            ->willReturn($currency);

        return $cart;
    }

    private function getCartItemMock(): CartItem
    {
        $cartItem = $this->getMockBuilder(CartItem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cartItem->expects($this->once())
            ->method('getId')
            ->willReturn(CartItemId::make(1));

        $cartItem->expects($this->once())
            ->method('getProduct')
            ->willReturn(1);

        $cartItem->expects($this->once())
            ->method('getQuantity')
            ->willReturn(1);

        $cartItem->expects($this->once())
            ->method('getSize')
            ->willReturn('size');

        $cartItem->expects($this->once())
            ->method('getColor')
            ->willReturn('color');

        return $cartItem;
    }

    private function registerCatalogueServiceMock(Currency $currency, CartItem $newCartItem): void
    {
        $this->productsService->expects($this->once())
            ->method('resolveCartItem')
            ->with(1, 1, $currency, 'size', 'color')
            ->willReturn($newCartItem);
    }
}