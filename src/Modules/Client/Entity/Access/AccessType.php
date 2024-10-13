<?php

namespace Project\Modules\Client\Entity\Access;

enum AccessType: string
{
    case PHONE = 'phone';
    case SOCIAL = 'social';
}
