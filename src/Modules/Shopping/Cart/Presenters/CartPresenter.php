<?php

namespace Project\Modules\Shopping\Cart\Presenters;

use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Common\Services\Environment\Language;
use Project\Modules\Shopping\Adapters\CatalogueService;

class CartPresenter implements CartPresenterInterface
{
    public function __construct(
        private CatalogueService $catalogueService
    ) {}

    public function present(DTO\Cart $cart, Language $language): array
    {
        $cartAsArray = $cart->toArray();
        $cartAsArray['offers'] = array_map(function (array $offer) use ($language) {
            $offer['content'] = $this->catalogueService->presentProduct(
                $offer['product'],
                $language
            );

            return $offer;
        }, $cartAsArray['offers']);

        return $cartAsArray;
    }
}