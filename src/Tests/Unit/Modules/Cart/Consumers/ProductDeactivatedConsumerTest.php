<?php

namespace Project\Tests\Unit\Modules\Cart\Consumers;

use Project\Common\Product\Availability;
use Project\Modules\Shopping\Entity\Offer;
use Project\Modules\Shopping\Entity\OfferId;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\DTO\Product\Product;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Catalogue\Product\Utils\ProductEntity2DTOConverter;
use Project\Modules\Shopping\Cart\Consumers\ProductDeactivatedConsumer;
use Project\Modules\Catalogue\Api\Events\Product\ProductActivityChanged;
use Project\Modules\Catalogue\Api\Events\Product\ProductAvailabilityChanged;

class ProductDeactivatedConsumerTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory;

    private Cart $cart;
    private OfferId $offerToDeleteId;
    private Offer $offerToDelete;
    private Offer $offerToIgnore;

    private CartsRepositoryInterface $carts;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->carts = $this->getMockBuilder(CartsRepositoryInterface::class)->getMock();
        $this->cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerToDeleteId = OfferId::random();
        $this->offerToDelete = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerToIgnore = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)->getMock();
    }

    public function testProductDeactivatedEvent()
    {
        $event = $this->getMockBuilder(ProductActivityChanged::class)
            ->disableOriginalConstructor()
            ->getMock();

        $product = $this->generateProduct();
        $product->deactivate();
        $productDTO = ProductEntity2DTOConverter::convert($product);

        $event->expects($this->exactly(4))
            ->method('getDTO')
            ->willReturn($productDTO);

        $this->mockRemoveProductFromCartsMethods($productDTO);

        $consumer = new ProductDeactivatedConsumer($this->carts);
        $consumer->setDispatcher($this->dispatcher);
        call_user_func($consumer, $event);
    }

    private function mockRemoveProductFromCartsMethods(Product $productDTO): void
    {
        $this->carts->expects($this->once())
            ->method('getCartsWithProduct')
            ->with($productDTO->id)
            ->willReturn([$this->cart, $this->cart]);

        $this->cart->expects($this->exactly(2))
            ->method('getOffers')
            ->willReturn([$this->offerToDelete, $this->offerToIgnore]);

        $this->offerToDelete->expects($this->exactly(2))
            ->method('getProduct')
            ->willReturn($productDTO->id);

        $this->offerToIgnore->expects($this->exactly(2))
            ->method('getProduct')
            ->willReturn($productDTO->id + 1);

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
        $event = $this->getMockBuilder(ProductActivityChanged::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productDTO = ProductEntity2DTOConverter::convert($this->generateProduct());
        $event->expects($this->once())
            ->method('getDTO')
            ->willReturn($productDTO);

        $consumer = new ProductDeactivatedConsumer($this->carts);
        $consumer->setDispatcher($this->dispatcher);
        call_user_func($consumer, $event);
    }

    public function testProductChangedToUnavailableEvent()
    {
        $event = $this->getMockBuilder(ProductAvailabilityChanged::class)
            ->disableOriginalConstructor()
            ->getMock();

        $product = $this->generateProduct();
        $product->setAvailability(Availability::OUT_STOCK);
        $productDTO = ProductEntity2DTOConverter::convert($product);

        $event->expects($this->exactly(4))
            ->method('getDTO')
            ->willReturn($productDTO);

        $this->mockRemoveProductFromCartsMethods($productDTO);

        $consumer = new ProductDeactivatedConsumer($this->carts);
        $consumer->setDispatcher($this->dispatcher);
        call_user_func($consumer, $event);
    }

    public function testProductChangedToAvailableEvent()
    {
        $event = $this->getMockBuilder(ProductAvailabilityChanged::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productDTO = ProductEntity2DTOConverter::convert($this->generateProduct());
        $event->expects($this->once())
            ->method('getDTO')
            ->willReturn($productDTO);

        $consumer = new ProductDeactivatedConsumer($this->carts);
        $consumer->setDispatcher($this->dispatcher);
        call_user_func($consumer, $event);
    }
}