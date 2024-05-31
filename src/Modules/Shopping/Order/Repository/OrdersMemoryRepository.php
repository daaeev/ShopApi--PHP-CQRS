<?php

namespace Project\Modules\Shopping\Order\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Modules\Shopping\Offers\Offer;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Common\Repository\NotFoundException;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\Repository\DuplicateKeyException;

class OrdersMemoryRepository implements OrdersRepositoryInterface
{
    private $offers = [];
    private int $increment = 0;

    public function __construct(
        private readonly Hydrator $hydrator,
        private readonly IdentityMap $identityMap,
    ) {}

    public function add(Order $order): void
    {
        if (null === $order->getId()->getId()) {
            $this->hydrator->hydrate($order->getId(), ['id' => ++$this->increment]);
        }

        $this->offers[$order->getId()->getId()] = $order->getOffers();
        foreach ($order->getOffers() as $offer) {
            if (null === $offer->getId()->getId()) {
                $this->hydrator->hydrate($offer->getId(), ['id' => ++$this->increment]);
            }

            $this->guardOfferIdIsUnique($offer);
        }

        if ($this->identityMap->has($order->getId()->getId())) {
            throw new DuplicateKeyException('Order with same id already exists');
        }

        $this->identityMap->add($order->getId()->getId(), $order);
    }

    private function guardOfferIdIsUnique(Offer $offer)
    {
        foreach ($this->offers as $orderOffers) {
            foreach ($orderOffers as $currentOffer) {
                if ($currentOffer === $offer) {
                    continue;
                }

                if ($offer->getId()->equalsTo($currentOffer->getId())) {
                    throw new DuplicateKeyException('Offer with same id already exists');
                }

                if ($offer->getUuid()->equalsTo($currentOffer->getUuid())) {
                    throw new DuplicateKeyException('Offer with same uuid already exists');
                }
            }
        }
    }

    public function update(Order $order): void
    {
        if (!$this->identityMap->has($order->getId()->getId())) {
            throw new NotFoundException('Order does not exists');
        }

        $this->offers[$order->getId()->getId()] = $order->getOffers();
        foreach ($order->getOffers() as $offer) {
            if (null === $offer->getId()->getId()) {
                $this->hydrator->hydrate($offer->getId(), ['id' => ++$this->increment]);
            }

            $this->guardOfferIdIsUnique($offer);
        }
    }

    public function delete(Order $order): void
    {
        if (!$this->identityMap->has($order->getId()->getId())) {
            throw new NotFoundException('Order does not exists');
        }

        $this->identityMap->remove($order->getId()->getId());
        unset($this->offers[$order->getId()->getId()]);
    }

    public function get(OrderId $id): Order
    {
        if (empty($id->getId())) {
            throw new NotFoundException('Order does not exists');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        throw new NotFoundException('Order does not exists');
    }
}