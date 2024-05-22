<?php

namespace Project\Modules\Shopping\Order\Utils;

use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Api\DTO\Order as DTO;
use Project\Modules\Shopping\Utils\OfferEntity2DTOConverter;

class OrderEntityToDTOConverter
{
    public static function convert(Entity\Order $order): DTO\Order
    {
        return new DTO\Order(
            id: $order->getId()->getId(),
            client: new DTO\ClientInfo(
                client: $order->getClient()->getClient(),
                firstName: $order->getClient()->getFirstName(),
                lastName: $order->getClient()->getLastName(),
                phone: $order->getClient()->getPhone(),
                email: $order->getClient()->getEmail(),
            ),
            status: $order->getStatus()->value,
            paymentStatus: $order->getPaymentStatus()->value,
            delivery: new DTO\DeliveryInfo(
                service: $order->getDelivery()->getService()->value,
                country: $order->getDelivery()->getCountry(),
                city: $order->getDelivery()->getCity(),
                street: $order->getDelivery()->getStreet(),
                houseNumber: $order->getDelivery()->getHouseNumber(),
            ),
            offers: array_map(
                fn (Offer $offer) => OfferEntity2DTOConverter::convert($offer),
                $order->getOffers()
            ),
            customerComment: $order->getCustomerComment(),
            managerComment: $order->getManagerComment(),
            createdAt: $order->getCreatedAt(),
            updatedAt: $order->getUpdatedAt(),
        );
    }
}