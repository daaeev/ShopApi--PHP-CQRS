<?php

namespace Project\Modules\Catalogue\Content\Services;

use Project\Common\Language;

interface ProductContentServiceInterface
{
    public function update(int $product, Language $language, array $fields): void;
}