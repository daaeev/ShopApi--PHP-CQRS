<?php

namespace Project\Tests\Unit\Modules\Helpers;

trait ReflectionHelper
{
    public function getPrivateProperty($object, $property)
    {
        $reflectedClass = new \ReflectionClass($object);
        $reflection = $reflectedClass->getProperty($property);
        $reflection->setAccessible(true);
        $value = $reflection->getValue($object);
        $reflection->setAccessible(false);
        return $value;
    }
}