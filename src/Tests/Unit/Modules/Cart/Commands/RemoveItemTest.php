<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Modules\Cart\Entity\CartId;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Modules\Cart\Adapters\ProductsService;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Cart\Commands\RemoveItemCommand;
use Project\Modules\Cart\Repository\MemoryCartRepository;
use Project\Modules\Cart\Repository\CartRepositoryInterface;
use Project\Modules\Cart\Commands\Handlers\RemoveItemHandler;

class RemoveItemTest extends \PHPUnit\Framework\TestCase
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

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)
            ->getMock();
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();

        parent::setUp();
    }

    public function testRemoveCartItemIfDoesNotExists()
    {
        $initialCart = $this->makeCart(
            CartId::next(),
            $this->client,
        );
        $initialCart->flushEvents();
        $this->carts->save($initialCart);

        $command = new RemoveItemCommand(
            1
        );
        $handler = new RemoveItemHandler(
            $this->carts,
            $this->environment
        );
        $handler->setDispatcher($this->dispatcher);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}