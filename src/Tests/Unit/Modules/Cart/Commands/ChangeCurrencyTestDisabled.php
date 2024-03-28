<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Product\Currency;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Adapters\CatalogueService;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Commands\ChangeCurrencyCommand;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
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
        $this->carts = new CartsMemoryRepository(new Hydrator, new IdentityMap);
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
        // Cart currency changed, Cart updated
        $this->dispatcher->expects($this->exactly(2))->method('dispatch');

        $cart = $this->makeCart(CartId::next(), $this->client);
        $cart->addItem($this->generateCartItem());
        $cart->addItem($this->generateCartItem());
        $cart->flushEvents();
        $this->carts->save($cart);

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with($cart);

        $this->productsService->expects($this->exactly(count($cart->getItems())))
            ->method('resolveCartItem')
            ->willReturnOnConsecutiveCalls(
                ...array_map([$this, 'generateCartItem'], $cart->getItems())
            );

        $command = new ChangeCurrencyCommand(Currency::USD->value);
        $handler = new ChangeCurrencyHandler($this->carts, $this->productsService, $this->discountsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->assertSame(Currency::USD, $cart->getCurrency());
    }
}