<?php

namespace Project\Modules\Shopping\Order\Entity;

enum PaymentStatus: string
{
    case PAID = 'paid';
    case NOT_PAID = 'not_paid';
}
