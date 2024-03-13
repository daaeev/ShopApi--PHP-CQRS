<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Product\Currency;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
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

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = new CartsMemoryRepository(new Hydrator, new IdentityMap);

        $this->productsService = $this->getMockBuilder(CatalogueService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)
            ->getMock();

        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();


        parent::setUp();
    }

    public function testChangeCurrencyWithEmptyCart()
    {
        $this->dispatcher->expects($this->once()) // Cart currency changed
            ->method('dispatch');

        $initialCart = $this->makeCart(
            CartId::next(),
            $this->client
        );
        $initialCart->flushEvents();
        $this->carts->save($initialCart);
        $this->assertNotSame(Currency::USD, $initialCart->getCurrency());

        $command = new ChangeCurrencyCommand(Currency::USD->value);
        $handler = new ChangeCurrencyHandler(
            $this->carts,
            $this->productsService,
            $this->environment
        );
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $cart = $this->carts->get($initialCart->getId());
        $this->assertSame(Currency::USD, $cart->getCurrency());
    }

    public function testChangeCurrencyWithNotEmptyCart()
    {
        $this->dispatcher->expects($this->exactly(2)) // Cart currency changed, Cart updated
            ->method('dispatch');

        $initialCart = $this->makeCart(
            CartId::next(),
            $this->client
        );
        $initialCart->addItem($this->generateCartItem());
        $initialCart->addItem($this->generateCartItem());
        $initialCart->flushEvents();
        $this->carts->save($initialCart);

        $this->productsService->expects($this->exactly(count($initialCart->getItems())))
            ->method('resolveCartItem')
            ->willReturnOnConsecutiveCalls(
                ...array_map([$this, 'generateCartItem'], $initialCart->getItems())
            );

        $this->assertNotSame(Currency::USD, $initialCart->getCurrency());

        $command = new ChangeCurrencyCommand(Currency::USD->value);
        $handler = new ChangeCurrencyHandler(
            $this->carts,
            $this->productsService,
            $this->environment
        );
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $cart = $this->carts->get($initialCart->getId());
        $this->assertSame(Currency::USD, $cart->getCurrency());
    }
}