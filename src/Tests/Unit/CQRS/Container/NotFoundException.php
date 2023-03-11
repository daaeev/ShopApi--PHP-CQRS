<?php

namespace Project\Tests\Unit\CQRS\Container;

use Exception;

class NotFoundException extends Exception implements \Psr\Container\NotFoundExceptionInterface
{
}