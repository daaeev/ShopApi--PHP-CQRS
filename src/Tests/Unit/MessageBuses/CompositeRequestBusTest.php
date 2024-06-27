<?php

namespace Project\Tests\Unit\MessageBuses;

use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Common\ApplicationMessages\Buses\CompositeRequestBus;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CompositeRequestBusTest extends \PHPUnit\Framework\TestCase
{
    public function testDispatchCommand()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
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
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
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
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $compositeBus = new CompositeRequestBus;
        $this->expectException(\DomainException::class);
        $compositeBus->dispatch($command);
    }

    public function testCanDispatch()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
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

    public function testCantDispatchWithoutRegisteredBusses()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $compositeBus = new CompositeRequestBus;
        $this->assertFalse($compositeBus->canDispatch($command));
    }

    public function testRegisterBus()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
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

    public function testRegisterNotRequestBus()
    {
        $busMock = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $compositeBus = new CompositeRequestBus;
        $this->expectException(\InvalidArgumentException::class);
        $compositeBus->registerBus($busMock);
    }
}