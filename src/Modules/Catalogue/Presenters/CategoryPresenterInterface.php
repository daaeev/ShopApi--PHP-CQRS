<?php

namespace Project\Modules\Catalogue\Presenters;

use Project\Modules\Catalogue\Api\DTO\Category as DTO;

interface CategoryPresenterInterface
{
    public function present(DTO\Category $product): array;
}