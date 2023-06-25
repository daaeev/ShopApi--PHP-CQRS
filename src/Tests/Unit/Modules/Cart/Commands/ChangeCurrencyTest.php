<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Product\Currency;
use Project\Modules\Cart\Entity\CartId;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Cart\Commands\ChangeCurrencyCommand;
use Project\Modules\Cart\Repository\MemoryCartRepository;
use Project\Modules\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Cart\Commands\Handlers\ChangeCurrencyHandler;

class ChangeCurrencyTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    private CartRepositoryInterface $carts;
    private EnvironmentInterface $environment;
    private Client $client;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()));
        $this->carts = new MemoryCartRepository(new Hydrator);

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)
            ->getMock();
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->once()) // Cart currency changed
            ->method('dispatch');

        parent::setUp();
    }

    public function testChangeCurrency()
    {
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
            $this->environment
        );
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $cart = $this->carts->get($initialCart->getId());
        $this->assertSame(Currency::USD, $cart->getCurrency());
    }
}