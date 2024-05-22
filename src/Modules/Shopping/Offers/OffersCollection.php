<?php

namespace Project\Modules\Shopping\Offers;

use Webmozart\Assert\Assert;
use Project\Common\Entity\Collections\Collection;

class OffersCollection extends Collection
{
    public function __construct(array $offers = [])
    {
        Assert::allIsInstanceOf($offers, Offer::class);
        parent::__construct($offers);
    }

    public function add(Offer $offer): void
    {
        if ($sameItem = $this->getSameOffer($offer)) {
            $this->replace($sameItem->getUuid(), $offer);
        } else {
            $this->entities[] = $offer;
        }
    }

    private function getSameOffer(Offer $offer): ?Offer
    {
        foreach ($this->entities as $currentOffer) {
            if ($offer->equalsTo($currentOffer)) {
                return $currentOffer;
            }
        }

        return null;
    }

    public function replace(OfferId|OfferUuId $oldOfferId, Offer $newOffer): void
    {
        foreach ($this->entities as $index => $offer) {
            $currentOfferId = $oldOfferId instanceof OfferId ? $offer->getId() : $offer->getUuid();
            if ($oldOfferId->equalsTo($currentOfferId)) {
                $this->entities[$index] = $newOffer;
                return;
            }
        }

        throw new \DomainException('Offer does not exists');
    }

    public function set(array $offers): void
    {
        Assert::allIsInstanceOf($offers, Offer::class);
        $this->entities = $offers;
    }

    public function remove(OfferId|OfferUuId $offerId): void
    {
        foreach ($this->entities as $index => $offer) {
            $currentOfferId = $offerId instanceof OfferId ? $offer->getId() : $offer->getUuid();
            if ($offerId->equalsTo($currentOfferId)) {
                unset($this->entities[$index]);
                return;
            }
        }

        throw new \DomainException("Offer does not exists");
    }

    public function get(OfferId|OfferUuId $offerId): Offer
    {
        foreach ($this->entities as $offer) {
            $currentOfferId = $offerId instanceof OfferId ? $offer->getId() : $offer->getUuid();
            if ($offerId->equalsTo($currentOfferId)) {
                return $offer;
            }
        }

        throw new \DomainException("Offer does not exists");
    }
}