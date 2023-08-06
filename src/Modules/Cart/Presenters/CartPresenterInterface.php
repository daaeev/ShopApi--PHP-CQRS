<?php

namespace Project\Modules\Cart\Presenters;

use Project\Common\Language;
use Project\Modules\Cart\Api\DTO;

interface CartPresenterInterface
{
    public function present(DTO\Cart $cart, Language $language): array;
}