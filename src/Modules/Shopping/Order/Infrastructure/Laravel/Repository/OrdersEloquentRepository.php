<?php

namespace Project\Modules\Shopping\Order\Infrastructure\Laravel\Repository;

use Ramsey\Uuid\Uuid;
use Project\Common\Client\Client;
use Project\Common\Repository\IdentityMap;
use Project\Modules\Shopping\Offers\Offer;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferUuId;
use Project\Modules\Shopping\Entity\Promocode;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Offers\OffersCollection;
use Project\Modules\Shopping\Order\Infrastructure\Laravel\Eloquent;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

class OrdersEloquentRepository implements OrdersRepositoryInterface
{
    public function __construct(
        private readonly IdentityMap $identityMap,
        private readonly Hydrator $hydrator,
    ) {}

    public function add(Entity\Order $order): void
    {
        $id = $order->getId()->getId();
        if (!empty($id) && $this->identityMap->has($id)) {
            throw new DuplicateKeyException('Order with same id already exists');
        }

        if (Eloquent\Order::find($id)) {
            throw new DuplicateKeyException('Order with same id already exists');
        }

        $this->persist($order, new Eloquent\Order);
        $this->identityMap->add($order->getId()->getId(), $order);
    }

    private function persist(Entity\Order $entity, Eloquent\Order $record): void
    {
        $this->persistOrder($entity, $record);
        $this->persistDelivery($entity, $record);
        $this->persistOffers($entity, $record);
    }

    private function persistOrder(Entity\Order $entity, Eloquent\Order $record): void
    {
        $record->id = $entity->getId()->getId();

        $record->client_id = $entity->getClient()->getClient()->getId();
        $record->client_hash = $entity->getClient()->getClient()->getHash();
        $record->manager_id = $entity->getManager()?->getId()?->getId();
        $record->manager_name = $entity->getManager()?->getName();
        $record->first_name = $entity->getClient()->getFirstName();
        $record->last_name = $entity->getClient()->getLastName();
        $record->phone = $entity->getClient()->getPhone();
        $record->email = $entity->getClient()->getEmail();

        $record->status = $entity->getStatus();
        $record->payment_status = $entity->getPaymentStatus();

        $record->currency = $entity->getCurrency();
        $record->total_price = $entity->getTotalPrice();
        $record->regular_price = $entity->getRegularPrice();

        $record->promocode_id = $entity->getPromocode()?->getId()?->getId();
        $record->promocode = $entity->getPromocode()?->getCode();
        $record->promocode_discount_percent = $entity->getPromocode()?->getDiscountPercent();

        $record->customer_comment = $entity->getCustomerComment();
        $record->manager_comment = $entity->getManagerComment();

        $record->created_at = $entity->getCreatedAt()->getTimestamp();
        $record->updated_at = $entity->getUpdatedAt()?->getTimestamp();

        $record->save();
        $this->hydrator->hydrate($entity->getId(), ['id' => $record->id]);
    }

    private function persistDelivery(Entity\Order $entity, Eloquent\Order $record): void
    {
        $record->delivery()->delete();
        $record->delivery()->create([
            'service' => $entity->getDelivery()->getService(),
            'country' => $entity->getDelivery()->getCountry(),
            'city' => $entity->getDelivery()->getCity(),
            'street' => $entity->getDelivery()->getStreet(),
            'house_number' => $entity->getDelivery()->getHouseNumber(),
        ]);
    }

    private function persistOffers(Entity\Order $entity, Eloquent\Order $record): void
    {
        $record->offers()->delete();
        foreach ($entity->getOffers() as $offer) {
            $this->guardOfferIdUnique($offer);
            $offerRecord = $record->offers()->create([
                'id' => $offer->getId()->getId(),
                'uuid' => $offer->getUuid()->getId(),
                'product_id' => $offer->getProduct(),
                'product_name' => $offer->getName(),
                'price' => $offer->getPrice(),
                'regular_price' => $offer->getRegularPrice(),
                'quantity' => $offer->getQuantity(),
                'size' => $offer->getSize(),
                'color' => $offer->getColor(),
            ]);

            $this->hydrator->hydrate($offer->getId(), ['id' => $offerRecord->id]);
        }
    }

    private function guardOfferIdUnique(Offer $offer): void
    {
        $notUnique = Eloquent\OrderOffer::query()
            ->where('id', $offer->getId()->getId())
            ->orWhere('uuid', $offer->getUuid()->getId())
            ->exists();

        if ($notUnique) {
            throw new DuplicateKeyException('Offer with same id already exists');
        }
    }

    public function update(Entity\Order $order): void
    {
        $id = $order->getId()->getId();
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Order not found');
        }

        if (!$record = Eloquent\Order::find($id)) {
            throw new NotFoundException('Order not found');
        }

        $this->persist($order, $record);
    }

    public function delete(Entity\Order $order): void
    {
        $id = $order->getId()->getId();
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Order not found');
        }

        if (!$record = Eloquent\Order::find($id)) {
            throw new NotFoundException('Order not found');
        }

        $record->delete();
        $this->identityMap->remove($id);
    }

    public function get(Entity\OrderId $id): Entity\Order
    {
        if (empty($id->getId())) {
            throw new NotFoundException('Order not found');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        if (empty($record = Eloquent\Order::find($id->getId()))) {
            throw new NotFoundException('Order does not exists');
        }

        $order = $this->convertRecordToEntity($record);
        $this->identityMap->add($order->getId()->getId(), $order);
        return $order;
    }

    private function convertRecordToEntity(Eloquent\Order $record): Entity\Order
    {
        return $this->hydrator->hydrate(Entity\Order::class, [
            'id' => Entity\OrderId::make($record->id),
            'client' => new Entity\ClientInfo(
                client: new Client($record->client_hash, $record->client_id),
                firstName: $record->first_name,
                lastName: $record->last_name,
                phone: $record->phone,
                email: $record->email,
            ),
            'manager' => $record->manager_id
                ? new Entity\Manager(
                    managerId: Entity\ManagerId::make($record->manager_id),
                    name: $record->manager_name
                )
                : null,
            'status' => $record->status,
            'paymentStatus' => $record->payment_status,
            'delivery' => new Entity\Delivery\DeliveryInfo(
                service: $record->delivery->service,
                country: $record->delivery->country,
                city: $record->delivery->city,
                street: $record->delivery->street,
                houseNumber: $record->delivery->house_number,
            ),
            'offers' => new OffersCollection(
                array_map([$this, 'convertOfferRecordToEntity'], $record->offers->all())
            ),
            'currency' => $record->currency,
            'totalPrice' => $record->total_price,
            'regularPrice' => $record->regular_price,
            'promocode' => !empty($record->promocode_id)
                ? new Promocode(
                    id: PromocodeId::make($record->promocode_id),
                    code: $record->promocode,
                    discountPercent: $record->promocode_discount_percent
                )
                : null,
            'customerComment' => $record->customer_comment,
            'managerComment' => $record->manager_comment,
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        ]);
    }

    private function convertOfferRecordToEntity(Eloquent\OrderOffer $record): Offer
    {
        return new Offer(
            id: OfferId::make($record->id),
            uuid: OfferUuId::make(Uuid::fromString($record->uuid)),
            product: $record->product_id,
            name: $record->product_name,
            regularPrice: $record->regular_price,
            price: $record->price,
            quantity: $record->quantity,
            size: $record->size,
            color: $record->color,
        );
    }
}