<?php

namespace Project\Modules\Shopping\Adapters;

use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferUuId;
use Project\Modules\Catalogue\Api\CatalogueApi;
use Project\Common\Services\Environment\Language;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Modules\Catalogue\Api\DTO\Product\Product as ProductDTO;

class CatalogueService
{
    public function __construct(
        private CatalogueApi $catalogue,
		private EnvironmentInterface $environment
    ) {}

	public function resolveOffer(
		int $productId,
		int $quantity,
		Currency $currency,
		?string $size = null,
		?string $color = null,
	): Offer {
		$product = $this->catalogue->get($productId, $this->environment->getLanguage())->product;
		$this->validateProduct($product, $size, $color);
        $price = $this->getPrice($product, $currency);
		return new Offer(
			OfferId::next(),
			OfferUuId::random(),
			$product->id,
			$product->name,
			$price,
			$price,
			$quantity,
			$size,
			$color
		);
	}

	private function validateProduct(ProductDTO $product, ?string $size, ?string $color): void
    {
        $this->guardProductAvailable($product);
		$this->guardProductHasSize($product, $size);
		$this->guardProductHasColor($product, $color);
	}

	private function guardProductAvailable(ProductDTO $product): void
	{
        $isAvailable = in_array(Availability::from($product->availability), Availability::available());
		if (!$product->active || !$isAvailable) {
			throw new \DomainException("Product #$product->id is not available");
		}
	}

	private function guardProductHasSize(ProductDTO $product, ?string $size): void
	{
		if (!empty($size) && !in_array($size, $product->sizes)) {
            throw new \DomainException("Product #$product->id does not has $size size");
		}
	}

	private function guardProductHasColor(ProductDTO $product, ?string $color): void
	{
		if (!empty($color) && !in_array($color, $product->colors)) {
            throw new \DomainException("Product #$product->id does not has $color color");
		}
	}

    private function getPrice(ProductDTO $product, Currency $currency): int
    {
        $price = null;
        foreach ($product->prices as $productPrice) {
            if ($productPrice->currency === $currency->value) {
                $price = $productPrice->price;
            }
        }

        if (null === $price) {
            throw new \DomainException("Product #$product->id does not has price in $currency->value currency");
        }

        return $price;
    }

    public function presentProduct(int $id, Language $language): array
    {
        return $this->catalogue->get($id, $language)->toArray();
    }
}