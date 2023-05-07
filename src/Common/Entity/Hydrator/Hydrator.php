<?php

namespace Project\Common\Entity\Hydrator;

use DomainException;

class Hydrator
{
    public function hydrate(string|object $objectOrClass, array $data): object
    {
        if (is_object($objectOrClass)) {
            $this->hydrateObject($objectOrClass, $data);
            return $objectOrClass;
        }

        if (is_string($objectOrClass)) {
            $object = $this->makeInstance($objectOrClass);
            $this->hydrateObject($object, $data);
            return $object;
        }

        throw new DomainException('Cant instantiate ' . $objectOrClass);
    }

    private function hydrateObject(object $instance, array $data): void
    {
        foreach ($data as $field => $value) {
            if (empty($field) || !is_string($field)) {
                throw new \DomainException('Invalid field name in hydrate data');
            }

            if (!property_exists($instance, $field)) {
                throw new \DomainException('Property ' . $field . ' does not exists in ' . $instance::class);
            }

            $reflection = new \ReflectionClass($instance);
            $property = $reflection->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($instance, $value);
        }
    }

    private function makeInstance(string $class): object
    {
        if (!class_exists($class)) {
            throw new \DomainException('Class ' . $class . ' does not exists');
        }

        $reflection = new \ReflectionClass($class);
        return $reflection->newInstanceWithoutConstructor();
    }
}