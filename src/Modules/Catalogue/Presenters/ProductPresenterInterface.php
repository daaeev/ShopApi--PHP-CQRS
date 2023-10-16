<?php

namespace Project\Modules\Catalogue\Presenters;

use Project\Modules\Catalogue\Api\DTO\Product as DTO;

interface ProductPresenterInterface
{
    public function present(DTO\Product $product): array;
}