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
            $this->replaceOffer($sameItem, $offer);
        } else {
            $this->entities[] = $offer;
        }
    }

    private function getSameOffer(Offer $offer): ?Offer
    {
        foreach ($this->entities as $currentOffer) {
            if ($offer->getId()->equalsTo($currentOffer->getId()) || $currentOffer->equalsTo($offer)) {
                return $currentOffer;
            }
        }

        return null;
    }

    private function replaceOffer(Offer $old, Offer $new): void
    {
        foreach ($this->entities as $index => $currentItem) {
            if ($old->getId()->equalsTo($currentItem->getId()) || $currentItem->equalsTo($old)) {
                $this->entities[$index] = $new;
            }
        }
    }

    public function set(array $offers): void
    {
        Assert::allIsInstanceOf($offers, Offer::class);
        $this->entities = $offers;
    }

    public function remove(OfferId $offerId): void
    {
        foreach ($this->entities as $index => $offer) {
            if ($offer->getId()->equalsTo($offerId)) {
                unset($this->entities[$index]);
                return;
            }
        }

        throw new \DomainException("Offer #{$offerId->getId()} does not exists");
    }

    public function get(OfferId $offerId): Offer
    {
        foreach ($this->entities as $offer) {
            if ($offer->getId()->equalsTo($offerId)) {
                return $offer;
            }
        }

        throw new \DomainException("Offer #{$offerId->getId()} does not exists");
    }
}