<?php

namespace Project\Common\Entity\Hydrator;

use DomainException;
use ReflectionClass;

class Hydrator
{
    public function hydrate(string|object $instance, array $data): object
    {
        if (is_object($instance)) {
            $this->hydrateObject($instance, $data);
            return $instance;
        }

        if (is_string($instance)) {
            $object = $this->makeInstance($instance);
            $this->hydrateObject($object, $data);
            return $object;
        }
    }

    private function hydrateObject(object $instance, array $data): void
    {
        foreach ($data as $field => $value) {
            if (empty($field) || !is_string($field)) {
                throw new DomainException('Invalid field name in hydrate data');
            }

            $reflection = new ReflectionClass($instance);
            $property = $reflection->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($value);
            $property->setAccessible(false);
        }
    }

    private function makeInstance(string $class): object
    {
        if (!class_exists($class)) {
            throw new DomainException('Class ' . $class . ' does not exists');
        }

        $reflection = new ReflectionClass($class);
        return $reflection->newInstanceWithoutConstructor();
    }
}