<?php

namespace Project\Modules\Shopping\Cart\Presenters;

use Project\Common\Language;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Modules\Shopping\Cart\Adapters\CatalogueService;

class CartPresenter implements CartPresenterInterface
{
    public function __construct(
        private CatalogueService $catalogueService
    ) {}

    public function present(DTO\Cart $cart, Language $language): array
    {
        $arrayCart = $cart->toArray();
        $arrayCart['items'] = array_map(function (array $item) use ($language) {
            $item['content'] = $this->catalogueService->presentProduct(
                $item['product'],
                $language
            );
            return $item;
        }, $arrayCart['items']);

        return $arrayCart;
    }
}