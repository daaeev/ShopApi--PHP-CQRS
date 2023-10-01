<?php

namespace Project\Tests\Unit\Modules\Client\Entity;

use Project\Modules\Client\Entity\ClientHash;

class ClientHashTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateWithEmptyHash()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ClientHash('');
    }
}