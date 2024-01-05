<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity;

enum PromotionStatus: string
{
    case NOT_STARTED = 'not-started';
    case STARTED = 'started';
    case ENDED = 'ended';
    case DISABLED = 'disabled';

    public static function calculate(Promotion $promotion): self
    {
        if ($promotion->disabled()) {
            return self::DISABLED;
        }

        if ($promotion->getDuration()->started()) {
            return self::STARTED;
        }

        if ($promotion->getDuration()->ended()) {
            return self::ENDED;
        }

        return self::NOT_STARTED;
    }
}
