<?php

namespace Project\Modules\Shopping\Order\Entity;

use Webmozart\Assert\Assert;
use Project\Common\Entity\Aggregate;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferUuId;
use Project\Modules\Shopping\Entity\Promocode;
use Project\Modules\Shopping\Offers\OffersCollection;
use Project\Modules\Shopping\Api\Events\Orders\OrderCreated;
use Project\Modules\Shopping\Api\Events\Orders\OrderUpdated;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryInfo;

class Order extends Aggregate
{
    private OrderId $id;
    private ClientInfo $client;
    private OrderStatus $status = OrderStatus::NEW;
    private PaymentStatus $paymentStatus = PaymentStatus::NOT_PAID;
    private DeliveryInfo $delivery;
    private OffersCollection $offers;
    private ?Promocode $promocode;
    private int $totalPrice; // Price with discounts
    private int $regularPrice; // Price without discount
    private ?string $customerComment = null;
    private ?string $managerComment = null;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        OrderId $id,
        ClientInfo $client,
        DeliveryInfo $delivery,
        OffersCollection $offers
    ) {
        $this->id = $id;
        $this->client = $client;
        $this->delivery = $delivery;
        $this->offers = $offers;
        $this->createdAt = new \DateTimeImmutable;
        $this->guardOffersCantBeEmpty();
        $this->refreshPrice();
        $this->addEvent(new OrderCreated($this));
    }

    private function guardOffersCantBeEmpty(): void
    {
        Assert::notEmpty($this->offers->all(), 'Order must contain at least 1 offer');
    }

    public function refreshPrice(): void
    {
        $this->refreshTotalPrice();
        $this->refreshRegularPrice();
    }

    private function refreshTotalPrice(): void
    {
        $totalPrice = array_reduce(
            array: $this->offers->all(),
            callback: fn ($totalPrice, Offer $offer) => $totalPrice + ($offer->getPrice() * $offer->getQuantity()),
            initial: 0
        );

        if (null !== $this->promocode) {
            $discountPrice = ($totalPrice / 100) * $this->promocode->getDiscountPercent();
            $totalPrice -= $discountPrice;
        }

        $this->totalPrice = $totalPrice;
    }

    private function refreshRegularPrice(): void
    {
        $this->regularPrice = array_reduce(
            array: $this->offers->all(),
            callback: fn ($totalPrice, Offer $offer) => $totalPrice + ($offer->getRegularPrice() * $offer->getQuantity()),
            initial: 0
        );
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
        $this->client = clone $this->client;
        $this->delivery = clone $this->delivery;
        $this->offers = clone $this->offers;
        $this->promocode = clone $this->promocode;
    }

    public function addOffer(Offer $offer): void
    {
        $this->offers->add($offer);
        $this->refreshPrice();
        $this->updated();
    }

    private function updated(): void
    {
        $this->addEvent(new OrderUpdated($this));
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function removeOffer(OfferUuId $offerId): void
    {
        $this->offers->remove($offerId);
        $this->guardOffersCantBeEmpty();
        $this->refreshPrice();
        $this->updated();
    }

    public function usePromocode(Promocode $promocode): void
    {
        if (null !== $this->promocode) {
            throw new \DomainException('Order already have other promocode');
        }

        if (empty($promocode->getId()->getId())) {
            throw new \DomainException('Promocode id cant be empty');
        }

        $this->promocode = $promocode;
        $this->refreshPrice();
        $this->updated();
    }

    public function removePromocode(): void
    {
        if (null === $this->promocode) {
            throw new \DomainException('Order does not have promocode');
        }

        $this->promocode = null;
        $this->refreshPrice();
        $this->updated();
    }

    public function updateClientInfo(ClientInfo $client): void
    {
        if (!$client->getClient()->same($this->client->getClient())) {
            throw new \DomainException('Cant attach order to another client id');
        }

        $this->client = $client;
        $this->updated();
    }

    public function updateStatus(OrderStatus $status): void
    {
        if ($this->status === $status) {
            throw new \DomainException('Order already have same status');
        }

        $this->status = $status;
        $this->updated();
    }

    public function updatePaymentStatus(PaymentStatus $status): void
    {
        if ($this->paymentStatus === $status) {
            throw new \DomainException('Order payment already have same status');
        }

        $this->paymentStatus = $status;
        $this->updated();
    }

    public function updateDelivery(DeliveryInfo $delivery): void
    {
        $this->delivery = $delivery;
        $this->updated();
    }

    public function addCustomerComment(string $comment): void
    {
        if (isset($this->customerComment)) {
            throw new \DomainException('Cant update existed customer comment');
        }

        Assert::notEmpty($comment);
        $this->customerComment = $comment;
        $this->updated();
    }

    public function updateManagerComment(?string $comment): void
    {
        $this->managerComment = $comment;
        $this->updated();
    }

    public function getId(): OrderId
    {
        return $this->id;
    }

    public function getClient(): ClientInfo
    {
        return $this->client;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getPaymentStatus(): PaymentStatus
    {
        return $this->paymentStatus;
    }

    public function getDelivery(): DeliveryInfo
    {
        return $this->delivery;
    }

    /**
     * @return Offer[]
     */
    public function getOffers(): array
    {
        return $this->offers->all();
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function getRegularPrice(): int
    {
        return $this->regularPrice;
    }

    public function getCustomerComment(): ?string
    {
        return $this->customerComment;
    }

    public function getManagerComment(): ?string
    {
        return $this->managerComment;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}