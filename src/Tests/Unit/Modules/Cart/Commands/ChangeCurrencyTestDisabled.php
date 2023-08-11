<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Product\Currency;
use Project\Modules\Cart\Entity\CartId;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Cart\Adapters\ProductsService;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Cart\Commands\ChangeCurrencyCommand;
use Project\Modules\Cart\Repository\MemoryCartRepository;
use Project\Modules\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Cart\Commands\Handlers\ChangeCurrencyHandler;

// Cant mock enums for test
class ChangeCurrencyTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    private CartRepositoryInterface $carts;
    private ProductsService $productsService;
    private EnvironmentInterface $environment;
    private Client $client;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()));
        $this->carts = new MemoryCartRepository(new Hydrator);

        $this->productsService = $this->getMockBuilder(ProductsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)
            ->getMock();
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
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