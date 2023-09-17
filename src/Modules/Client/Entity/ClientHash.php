<?php

namespace Project\Modules\Client\Entity;

use Webmozart\Assert\Assert;
use Project\Common\Entity\Id\StringId;

class ClientHash extends StringId
{
    public function __construct(string $id = null)
    {
        Assert::notEmpty($id, 'Client hash cant be empty');
        parent::__construct($id);
    }
}