<?php

namespace Project\Modules\Shopping\Presenters;

use Project\Modules\Shopping\Api\DTO\Order as DTO;

interface OrderPresenterInterface
{
    public function present(DTO\Order $order): array;
}