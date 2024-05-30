<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Modules\Shopping\Order\Entity\ClientInfo;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryInfo;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;

trait OrderFactory
{
    private function generateOrder(array $offers): Order
    {
        $order = new Order(
            id: OrderId::random(),
            client: new ClientInfo(
                client: new Client(md5(rand()), rand(1, 99999)),
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
            offers: $offers,
            currency: Currency::default()
        );

        $order->flushEvents();
        return $order;
    }

    private function makeOrder(
        OrderId $id,
        ClientInfo $client,
        DeliveryInfo $delivery,
        array $offers,
        Currency $currency,
    ): Order {
        return new Order($id, $client, $delivery, $offers, $currency);
    }
}