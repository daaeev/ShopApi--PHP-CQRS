<?php

namespace Project\Modules\Shopping\Cart\Entity;

use Project\Common\Client\Client;
use Project\Common\Entity\Aggregate;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferUuId;
use Project\Modules\Shopping\Entity\Promocode;
use Project\Modules\Shopping\Offers\OffersCollection;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Modules\Shopping\Api\Events\Cart\CartInstantiated;
use Project\Modules\Shopping\Api\Events\Cart\CartCurrencyChanged;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeAddedToCart;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeRemovedFromCart;

class Cart extends Aggregate
{
    private CartId $id;
    private Client $client;
    private OffersCollection $offers;
    private Currency $currency;
    private int $totalPrice; // Price with discounts
    private int $regularPrice; // Price without discount
    private ?Promocode $promocode = null;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(CartId $id, Client $client) {
        $this->id = $id;
        $this->client = $client;
        $this->offers = new OffersCollection;
        $this->currency = Currency::default();
        $this->createdAt = new \DateTimeImmutable;
        $this->refreshPrice();
        $this->addEvent(new CartInstantiated($this));
    }

	public function __clone(): void
	{
		$this->id = clone $this->id;
		$this->client = clone $this->client;
        $this->offers = clone $this->offers;
		$this->promocode = $this->promocode ? clone $this->promocode : null;
		$this->createdAt = clone $this->createdAt;
		$this->updatedAt = $this->updatedAt ? clone $this->updatedAt : null;
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

        $this->totalPrice = (int) $totalPrice;
    }

    private function refreshRegularPrice(): void
    {
        $this->regularPrice = array_reduce(
            array: $this->offers->all(),
            callback: fn ($totalPrice, Offer $offer) => $totalPrice + ($offer->getRegularPrice() * $offer->getQuantity()),
            initial: 0
        );
    }

	public static function instantiate(Client $client): self
    {
        return new self(CartId::next(), $client);
    }

    public function addOffer(Offer $offer): void
    {
        $this->offers->add($offer);
        $this->refreshPrice();
        $this->updated();
    }

    public function replaceOffer(Offer $offer, Offer $newOffer): void
    {
        $this->offers->replace($offer->getUuid(), $newOffer);
        $this->refreshPrice();
        $this->updated();
    }

    public function getOffer(OfferId|OfferUuId $offerId): Offer
    {
        return $this->offers->get($offerId);
    }

    public function removeOffer(OfferId|OfferUuId $offerId): void
    {
        $this->offers->remove($offerId);
        $this->refreshPrice();
        $this->updated();
    }

    private function updated(): void
    {
        $this->addEvent(new CartUpdated($this));
        $this->updatedAt = new \DateTimeImmutable;
    }

	public function setOffers(array $offers): void
	{
		$this->offers->set($offers);
        $this->refreshPrice();
        $this->updated();
	}

    public function changeCurrency(Currency $currency): void
    {
        if ($currency === $this->currency) {
            return;
        }

        if (!$currency->isActive()) {
            throw new \DomainException('Cant update cart currency to inactive currency ');
        }

        $this->currency = $currency;
        $this->addEvent(new CartCurrencyChanged($this));
        $this->updated();
    }

    public function usePromocode(Promocode $promocode): void
    {
        if (null !== $this->promocode) {
            throw new \DomainException('Other promocode already used');
        }

        if (empty($promocode->getId()->getId())) {
            throw new \DomainException('Promocode id cant be empty');
        }

        $this->promocode = $promocode;
        $this->refreshPrice();
        $this->addEvent(new PromocodeAddedToCart($this));
        $this->updated();
    }

    public function removePromocode(): void
    {
        if (null === $this->promocode) {
            throw new \DomainException('Cart does not have promo-code');
        }

        $this->promocode = null;
        $this->refreshPrice();
        $this->addEvent(new PromocodeRemovedFromCart($this));
        $this->updated();
    }

    public function getId(): CartId
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return Offer[]
     */
    public function getOffers(): array
    {
        return $this->offers->all();
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function getRegularPrice(): int
    {
        return $this->regularPrice;
    }

    public function getPromocode(): ?Promocode
    {
        return $this->promocode;
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