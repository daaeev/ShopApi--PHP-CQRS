<?php

namespace Project\Modules\Shopping\Cart\Presenters;

use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Common\Services\Environment\Language;

interface CartPresenterInterface
{
    public function present(DTO\Cart $cart, Language $language): array;
}