<?php

namespace Project\Tests\Unit\Modules\Orders\Entity;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Tests\Unit\Modules\Helpers\OrderFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Shopping\Order\Entity\ClientInfo;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Modules\Shopping\Order\Entity\OrderStatus;
use Project\Modules\Shopping\Order\Entity\PaymentStatus;
use Project\Modules\Shopping\Api\Events\Orders\OrderCreated;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryInfo;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;

class CreateOrderTest extends \PHPUnit\Framework\TestCase
{
    use OrderFactory, OffersFactory, AssertEvents;

    public function testCreate()
    {
        $order = $this->makeOrder(
            $id = OrderId::random(),
            $client = new ClientInfo(
                client: new Client(hash: md5(rand()), id: rand()),
                firstName: md5(rand()),
                lastName: md5(rand()),
                phone: md5(rand()),
                email: md5(rand()),
            ),
            $delivery = new DeliveryInfo(
                service: DeliveryService::NOVA_POST,
                country: md5(rand()),
                city: md5(rand()),
                street: md5(rand()),
                houseNumber: md5(rand()),
            ),
            $offers = [$this->generateOffer()],
            $currency = Currency::default()
        );

        $this->assertTrue($order->getId()->equalsTo($id));
        $this->assertTrue($order->getClient()->equalsTo($client));
        $this->assertSame(OrderStatus::NEW, $order->getStatus());
        $this->assertSame(PaymentStatus::NOT_PAID, $order->getPaymentStatus());
        $this->assertTrue($order->getDelivery()->equalsTo($delivery));
        $this->assertSame($offers, $order->getOffers());
        $this->assertSame($currency, $order->getCurrency());
        $this->assertNull($order->getPromocode());
        $this->assertSame($this->calculateTotalPrice($offers), $order->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($offers), $order->getRegularPrice());
        $this->assertNull($order->getCustomerComment());
        $this->assertNull($order->getManagerComment());
        $this->assertNotNull($order->getCreatedAt());
        $this->assertNull($order->getUpdatedAt());

        $this->assertEvents($order, [new OrderCreated($order)]);
    }

    public function testCreateOrderWithoutOffers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeOrder(
            id: OrderId::random(),
            client: new ClientInfo(
                client: new Client(hash: md5(rand()), id: rand()),
                firstName: md5(rand()),
                lastName: md5(rand()),
                phone: md5(rand()),
                email: md5(rand()),
            ),
            delivery: new DeliveryInfo(
                service: DeliveryService::NOVA_POST,
                country: md5(rand()),
                city: md5(rand()),
                street: md5(rand()),
                houseNumber: md5(rand()),
            ),
            offers: [],
            currency: Currency::default()
        );
    }
}