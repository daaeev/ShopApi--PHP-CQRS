<?php

namespace Project\Modules\Administrators\Entity;

use Project\Common\Events;

class Admin implements Events\EventRoot
{
    use Events\EventTrait;
}