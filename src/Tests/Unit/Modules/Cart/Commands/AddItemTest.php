<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Adapters\CatalogueService;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Cart\Commands\AddItemCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
use Project\Modules\Shopping\Cart\Commands\Handlers\AddItemHandler;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;

class AddItemTest extends \PHPUnit\Framework\TestCase
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

    public function testAddItem()
    {
        // Cart updated
        $this->dispatcher->expects($this->once())->method('dispatch');

        $cart = $this->carts->getActiveCart($this->client);
        $cart->flushEvents();
        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with($cart);

        $command = new AddItemCommand(
            $product = rand(1, 10),
            $quantity = rand(1, 10),
            $size = md5(rand()),
            $color = md5(rand()),
        );

        $this->productsService->expects($this->once())
            ->method('resolveCartItem')
            ->with($product, $quantity, Currency::default(), $size, $color, true)
            ->willReturn($cartItem = $this->generateCartItem());

		$handler = new AddItemHandler($this->carts, $this->productsService, $this->discountsService, $this->environment);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->assertCount(1, $cart->getItems());
        $addedItem = $cart->getItem($cartItem->getId());
        $this->assertSame($cartItem, $addedItem);
    }
}