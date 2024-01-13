<?php

namespace Project\Tests\Unit\CQRS;

use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Common\ApplicationMessages\Buses\CompositeRequestBus;

class CompositeRequestBusTest extends \PHPUnit\Framework\TestCase
{
    public function testDispatchCommand()
    {
        $command = new \stdClass;
        $busMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);

        $busMock->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn('Success');

        $compositeBus = new CompositeRequestBus;
        $compositeBus->registerBus($busMock);
        $this->assertEquals('Success', $compositeBus->dispatch($command));
    }

    public function testDispatchCommandWithOneBusThatCantDispatchEvent()
    {
        $command = new \stdClass;
        $busMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(false);

        $compositeBus = new CompositeRequestBus;
        $compositeBus->registerBus($busMock);
        $this->expectException(\DomainException::class);
        $compositeBus->dispatch($command);
    }

    public function testDispatchCommandWithoutRegisteredBusses()
    {
        $command = new \stdClass;
        $compositeBus = new CompositeRequestBus;
        $this->expectException(\DomainException::class);
        $compositeBus->dispatch($command);
    }

    public function testCanDispatch()
    {
        $command = new \stdClass;
        $busMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);

        $compositeBus = new CompositeRequestBus;
        $compositeBus->registerBus($busMock);
        $this->assertTrue($compositeBus->canDispatch($command));
    }

    public function testCantDispatchWithOneRegisteredBus()
    {
        $command = new \stdClass;
        $busMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(false);

        $compositeBus = new CompositeRequestBus;
        $compositeBus->registerBus($busMock);
        $this->assertFalse($compositeBus->canDispatch($command));
    }

    public function testCantDispatchWithoutRegisteredBusses()
    {
        $command = new \stdClass;
        $compositeBus = new CompositeRequestBus;
        $this->assertFalse($compositeBus->canDispatch($command));
    }

    public function testRegisterBus()
    {
        $command = new \stdClass;
        $busMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);

        $compositeBus = new CompositeRequestBus;
        $this->assertFalse($compositeBus->canDispatch($command));
        $compositeBus->registerBus($busMock);
        $this->assertTrue($compositeBus->canDispatch($command));
    }
}