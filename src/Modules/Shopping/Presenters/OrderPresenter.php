<?php

namespace Project\Modules\Shopping\Presenters;

use Project\Common\Language;
use Project\Modules\Shopping\Api\DTO\Order as DTO;
use Project\Modules\Shopping\Adapters\CatalogueService;

class OrderPresenter implements OrderPresenterInterface
{
    public function __construct(
        private readonly CatalogueService $catalogue,
    ) {}

    public function present(DTO\Order $order): array
    {
        $presented = $order->toArray();
        foreach ($presented['offers'] as &$offer) {
            $offer['content'] = $this->catalogue->presentProduct($offer['product'], Language::default());
        }

        return $presented;
    }
}