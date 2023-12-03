<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity;

enum PromotionStatus: string
{
    case NOT_STARTED = 'not-started';
    case STARTED = 'started';
    case ENDED = 'ended';
    case DISABLED = 'disabled';
}
