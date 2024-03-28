<?php

namespace Project\Tests\Unit\Modules\Cart\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Environment\Client\Client;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Common\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Cart\Commands\UsePromocodeCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsMemoryRepository;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Commands\Handlers\UsePromocodeHandler;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

class UsePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, PromocodeFactory;

    private CartsRepositoryInterface $carts;
    private PromocodesRepositoryInterface $promocodes;
    private EnvironmentInterface $environment;
    private Client $client;
    private MessageBusInterface $dispatcher;
    private DiscountsService $discountsService;

    protected function setUp(): void
    {
        $this->client = new Client(md5(rand()), rand(1, 100));
        $this->carts = new CartsMemoryRepository(new Hydrator, new IdentityMap);
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        parent::setUp();
    }

    public function testUsePromocode()
    {
        // Promocode added, Cart updated
        $this->dispatcher->expects($this->exactly(2))->method('dispatch');

        $cart = $this->carts->getActiveCart($this->client);
        $cart->flushEvents();

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with($cart);

        $promocode = $this->generatePromocode();
        $this->promocodes->add($promocode);

        $command = new UsePromocodeCommand($promocode->getCode());
		$handler = new UsePromocodeHandler(
            $this->carts,
            $this->environment,
            $this->promocodes,
            $this->discountsService,
        );

        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->assertSame($promocode, $cart->getPromocode());
    }
}