<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Product\Currency;
use Project\Common\Services\Environment\Client;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Modules\Shopping\Order\Entity\ClientInfo;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryInfo;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;

trait OrderFactory
{
    use ContactsGenerator;

    private function generateOrder(array $offers, ?OrderId $orderId = null): Order
    {
        $order = new Order(
            id: $orderId ?? OrderId::random(),
            client: new ClientInfo(
                client: new Client(uniqid(), rand(1, 99999)),
                firstName: uniqid(),
                lastName: uniqid(),
                phone: $this->generatePhone(),
                email: $this->generateEmail(),
            ),
            delivery: new DeliveryInfo(
                service: DeliveryService::NOVA_POST,
                country: uniqid(),
                city: uniqid(),
                street: uniqid(),
                houseNumber: uniqid(),
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