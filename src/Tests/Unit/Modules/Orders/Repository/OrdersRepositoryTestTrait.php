<?php

namespace Project\Tests\Unit\Modules\Orders\Repository;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Entity\Promocode;
use Project\Modules\Shopping\Offers\OfferUuId;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\OrderFactory;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Modules\Shopping\Order\Entity\ClientInfo;
use Project\Modules\Shopping\Order\Entity\OrderStatus;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Order\Entity\PaymentStatus;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryInfo;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;

trait OrdersRepositoryTestTrait
{
    use OrderFactory, OffersFactory, PromocodeFactory;

    protected OrdersRepositoryInterface $orders;

    public function testAdd()
    {
        $initial = $this->generateOrder([$this->generateOffer()]);
        $initial->usePromocode(Promocode::fromBaseEntity($this->generatePromocode()));
        $initial->addCustomerComment(uniqid());
        $initial->updateManagerComment(uniqid());
        $initialAsString = serialize($initial);
        $this->orders->add($initial);

        $found = $this->orders->get($initial->getId());
        $this->assertSame($initial, $found);
        $this->assertSame($initialAsString, serialize($found));
    }

    public function testAddIncrementIds()
    {
        $order = $this->makeOrder(
            id: OrderId::next(),
            client: new ClientInfo(
                client: new Client(hash: uniqid(), id: rand()),
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
            offers: [$offer = $this->makeOffer(id: OfferId::next(), uuid: OfferUuId::random())],
            currency: Currency::default()
        );

        $this->orders->add($order);
        $this->assertNotNull($order->getId()->getId());
        $this->assertNotNull($offer->getId()->getId());
    }

    public function testAddWithDuplicatedOrderId()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $orderWithSameId = $this->generateOrder([$this->generateOffer()], $order->getId());
        $this->orders->add($order);

        $this->expectException(DuplicateKeyException::class);
        $this->orders->add($orderWithSameId);
    }

    public function testAddWithDuplicatedOfferId()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer()]);
        $this->orders->add($order);

        $otherOrder = $this->generateOrder([$this->makeOffer($offer->getId(), OfferUuId::random())]);
        $this->expectException(DuplicateKeyException::class);
        $this->orders->add($otherOrder);
    }

    public function testAddWithDuplicatedOfferUuid()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer()]);
        $this->orders->add($order);

        $otherOrder = $this->generateOrder([$this->makeOffer(OfferId::random(), $offer->getUuid())]);
        $this->expectException(DuplicateKeyException::class);
        $this->orders->add($otherOrder);
    }

    public function testUpdate()
    {
        $initial = $this->generateOrder([$this->generateOffer()]);
        $initialAsString = serialize($initial);
        $this->orders->add($initial);

        $added = $this->orders->get($initial->getId());
        $added->addOffer($this->generateOffer());
        $added->updateStatus(OrderStatus::IN_PROGRESS);
        $added->updatePaymentStatus(PaymentStatus::PAID);
        $added->usePromocode(Promocode::fromBaseEntity($this->generatePromocode()));
        $added->addCustomerComment(uniqid());
        $added->updateManagerComment(uniqid());
        $added->updateClientInfo(new ClientInfo(
            client: $added->getClient()->getClient(),
            firstName: uniqid(),
            lastName: uniqid(),
            phone: $this->generatePhone(),
            email: $this->generateEmail(),
        ));

        $added->updateDelivery(new DeliveryInfo(
            service: DeliveryService::NOVA_POST,
            country: uniqid(),
            city: uniqid(),
            street: uniqid(),
            houseNumber: uniqid(),
        ));

        $addedAsString = serialize($added);
        $this->orders->update($added);

        $updated = $this->orders->get($initial->getId());
        $updatedAsString = serialize($updated);
        $this->assertSame($initial, $added);
        $this->assertSame($added, $updated);
        $this->assertNotSame($initialAsString, $addedAsString);
        $this->assertNotSame($initialAsString, $updatedAsString);
        $this->assertSame($addedAsString, $updatedAsString);
    }

    public function testUpdateIncrementOfferIds()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $this->orders->add($order);

        $order->addOffer($offer = $this->makeOffer(OfferId::next(), OfferUuId::random()));
        $this->orders->update($order);
        $this->assertNotNull($offer->getId()->getId());
    }

    public function testUpdateWithDuplicatedOfferId()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer()]);
        $otherOrder = $this->generateOrder([$this->generateOffer()]);
        $this->orders->add($order);
        $this->orders->add($otherOrder);

        $otherOrder->addOffer($this->makeOffer($offer->getId(), OfferUuId::random()));
        $this->expectException(DuplicateKeyException::class);
        $this->orders->update($otherOrder);
    }

    public function testUpdateWithDuplicatedOfferUuid()
    {
        $order = $this->generateOrder([$offer = $this->generateOffer()]);
        $otherOrder = $this->generateOrder([$this->generateOffer()]);
        $this->orders->add($order);
        $this->orders->add($otherOrder);

        $otherOrder->addOffer($this->makeOffer(OfferId::random(), $offer->getUuid()));
        $this->expectException(DuplicateKeyException::class);
        $this->orders->update($otherOrder);
    }

    public function testUpdateIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $order = $this->generateOrder([$this->generateOffer()]);
        $this->orders->update($order);
    }

    public function testDelete()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $this->orders->add($order);
        $this->orders->delete($order);

        $this->expectException(NotFoundException::class);
        $this->orders->get($order->getId());
    }

    public function testDeleteIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $order = $this->generateOrder([$this->generateOffer()]);
        $this->orders->delete($order);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->orders->get(OrderId::random());
    }
}
