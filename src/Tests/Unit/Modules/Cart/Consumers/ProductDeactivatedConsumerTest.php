<?php

namespace Project\Tests\Unit\Modules\Cart\Consumers;

use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Cart\Consumers\ProductDeactivatedConsumer;
use Project\Modules\Shopping\Adapters\Events\ProductDeactivatedDeserializer;

class ProductDeactivatedConsumerTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory;

    private CartsRepositoryInterface $carts;
    private ProductDeactivatedDeserializer $deserializer;
    private MessageBusInterface $dispatcher;

    private Cart $cart;
    private int $productId;
    private OfferId $offerToDeleteId;
    private Offer $offerToDelete;
    private Offer $offerToIgnore;

    protected function setUp(): void
    {
        $this->carts = $this->getMockBuilder(CartsRepositoryInterface::class)->getMock();
        $this->deserializer = $this->getMockBuilder(ProductDeactivatedDeserializer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productId = random_int(1, 100);
        $this->offerToDeleteId = OfferId::random();
        $this->offerToDelete = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerToIgnore = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testProductDeactivatedEvent()
    {
        $this->deserializer->expects($this->exactly(2))
            ->method('activityChanged')
            ->willReturn(true);

        $this->deserializer->expects($this->once())
            ->method('isProductActive')
            ->willReturn(false);

        $this->deserializer->expects($this->exactly(3))
            ->method('getProductId')
            ->willReturn($this->productId);

        $this->mockRemoveProductFromCartsMethods();

        $consumer = new ProductDeactivatedConsumer($this->carts);
        $consumer->setDispatcher($this->dispatcher);
        call_user_func($consumer, $this->deserializer);
    }

    private function mockRemoveProductFromCartsMethods(): void
    {
        $this->carts->expects($this->once())
            ->method('getCartsWithProduct')
            ->with($this->productId)
            ->willReturn([$this->cart, $this->cart]);

        $this->cart->expects($this->exactly(2))
            ->method('getOffers')
            ->willReturn([$this->offerToDelete, $this->offerToIgnore]);

        $this->offerToDelete->expects($this->exactly(2))
            ->method('getProduct')
            ->willReturn($this->productId);

        $this->offerToIgnore->expects($this->exactly(2))
            ->method('getProduct')
            ->willReturn($this->productId + 1);

        $this->offerToDelete->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->offerToDeleteId);

        $this->cart->expects($this->exactly(2))
            ->method('removeOffer')
            ->with($this->offerToDeleteId);

        $this->carts->expects($this->exactly(2))
            ->method('save')
            ->with($this->cart);

        $this->cart->expects($this->exactly(2))
            ->method('flushEvents')
            ->willReturn([$cartUpdatedEvent = new CartUpdated($this->cart)]);

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with($cartUpdatedEvent);
    }

    public function testProductActivatedEvent()
    {
        $this->deserializer->expects($this->once())
            ->method('activityChanged')
            ->willReturn(true);

        $this->deserializer->expects($this->once())
            ->method('isProductActive')
            ->willReturn(true);

        $consumer = new ProductDeactivatedConsumer($this->carts);
        $consumer->setDispatcher($this->dispatcher);
        call_user_func($consumer, $this->deserializer);
    }

    public function testProductChangedToUnavailableEvent()
    {
        $this->deserializer->expects($this->exactly(2))
            ->method('activityChanged')
            ->willReturn(false);

        $this->deserializer->expects($this->once())
            ->method('isProductAvailable')
            ->willReturn(false);

        $this->deserializer->expects($this->exactly(3))
            ->method('getProductId')
            ->willReturn($this->productId);

        $this->mockRemoveProductFromCartsMethods();

        $consumer = new ProductDeactivatedConsumer($this->carts);
        $consumer->setDispatcher($this->dispatcher);
        call_user_func($consumer, $this->deserializer);
    }

    public function testProductChangedToAvailableEvent()
    {
        $this->deserializer->expects($this->exactly(2))
            ->method('activityChanged')
            ->willReturn(false);

        $this->deserializer->expects($this->once())
            ->method('isProductAvailable')
            ->willReturn(true);

        $consumer = new ProductDeactivatedConsumer($this->carts);
        $consumer->setDispatcher($this->dispatcher);
        call_user_func($consumer, $this->deserializer);
    }
}