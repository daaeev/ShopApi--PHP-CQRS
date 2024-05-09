<?php

namespace Project\Tests\Unit\Entity\Hydrator;

use Project\Common\Entity\Hydrator\Hydrator;

class HydratorTest extends \PHPUnit\Framework\TestCase
{
    private Hydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new Hydrator;
    }

    public function testObjectReturnSameValue()
    {
        $initValue = 'Init value';
        $object = new TestObject($initValue);
        $this->assertSame($initValue, $object->getInitPrivateValue());
    }

    public function testHydrateUsingClassName()
    {
        $privateValue = 'Private value';
        $object = $this->hydrator->hydrate(TestObject::class, [
            'initPrivateValue' => $privateValue
        ]);
        $this->assertInstanceOf(TestObject::class, $object);
        $this->assertSame($privateValue, $object->getInitPrivateValue());
    }

    public function testHydrateUsingInstance()
    {
        $initValue = 'Init value';
        $hydratedValue = 'Hydrated value';
        $object = new TestObject($initValue);
        $this->assertSame($initValue, $object->getInitPrivateValue());
        $this->assertNotSame($hydratedValue, $object->getInitPrivateValue());
        $this->hydrator->hydrate($object, ['initPrivateValue' => $hydratedValue]);
        $this->assertNotSame($initValue, $object->getInitPrivateValue());
        $this->assertSame($hydratedValue, $object->getInitPrivateValue());
    }

    public function testWithNotInstantiatableEntity()
    {
        $this->expectException(\DomainException::class);
        $this->hydrator->hydrate(
            TestInterface::class,
            ['test' => 1]
        );
    }

    public function testClassDoesNotExists()
    {
        $this->expectException(\DomainException::class);
        $this->hydrator->hydrate(
            ClassDoesNotExists::class,
            ['test' => 1]
        );
    }

    public function testWithNotStringFieldName()
    {
        $this->expectException(\DomainException::class);
        $this->hydrator->hydrate(
            TestObject::class,
            [1 => 1]
        );
    }

    public function testIfPropertyDoesNotExists()
    {
        $this->expectException(\DomainException::class);
        $this->hydrator->hydrate(
            TestObject::class,
            ['PropertyDoesNotExists' => 1]
        );
    }
}