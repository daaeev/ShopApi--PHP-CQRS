<?php

namespace Project\Modules\Shopping\Cart\Presenters;

use Project\Common\Language;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;

interface CartPresenterInterface
{
    public function present(DTO\Cart $cart, Language $language): array;
}