<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Cart\Adapters\ProductsService;
use Project\Modules\Shopping\Cart\Commands\RemoveItemCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\RemoveItemHandler;

class RemoveItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    private CartsRepositoryInterface $carts;
    private ProductsService $productsService;
    private EnvironmentInterface $environment;
    private Client $client;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = new CartsMemoryRepository(new Hydrator);

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)
            ->getMock();
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
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