<?php

namespace Project\Modules\Shopping\Order\Infrastructure\Laravel\Utils;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Api\DTO\Offer;
use Project\Modules\Shopping\Api\DTO\Promocode;
use Project\Modules\Shopping\Api\DTO\Order as DTO;
use Project\Modules\Shopping\Order\Infrastructure\Laravel\Eloquent;

class OrderEloquentToDTOConverter
{
    public static function convert(Eloquent\Order $record): DTO\Order
    {
        return new DTO\Order(
            id: $record->id,
            client: new DTO\ClientInfo(
                client: new Client(hash: $record->client_hash, id: $record->client_id),
                firstName: $record->first_name,
                lastName: $record->last_name,
                phone: $record->phone,
                email: $record->email,
            ),
            status: $record->status->value,
            paymentStatus: $record->payment_status->value,
            delivery: new DTO\DeliveryInfo(
                service: $record->delivery->service->value,
                country: $record->delivery->country,
                city: $record->delivery->city,
                street: $record->delivery->street,
                houseNumber: $record->delivery->house_number,
            ),
            offers: array_map('self::convertOffer', $record->offers->all()),
            currency: $record->currency->value,
            totalPrice: $record->total_price,
            regularPrice: $record->regular_price,
            promocode: $record->promocode_id
                ? new Promocode(
                    id: $record->promocode_id,
                    code: $record->promocode,
                    discountPercent: $record->promocode_discount_percent,
                )
                : null,
            customerComment: $record->customer_comment,
            managerComment: $record->manager_comment,
            createdAt: new \DateTimeImmutable($record->created_at),
            updatedAt: $record->updated_at ? new \DateTimeImmutable($record->updated_at) : null
        );
    }

    public static function convertOffer(Eloquent\OrderOffer $offer): Offer
    {
        return new Offer(
            id: $offer->id,
            uuid: $offer->uuid,
            product: $offer->product_id,
            name: $offer->product_name,
            regularPrice: $offer->regular_price,
            price: $offer->price,
            quantity: $offer->quantity,
            size: $offer->size,
            color: $offer->color,
        );
    }
}