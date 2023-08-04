<?php

namespace Project\Tests\Laravel\CQRS;

use Illuminate\Support\Facades\DB;
use Project\Common\CQRS\Buses\CompositeRequestBus;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Infrastructure\Laravel\CQRS\Buses\Decorators\TransactionBus;
use Project\Tests\Unit\CQRS\Commands\TestCommand;

class TransactionCompositeBusTest extends \Tests\TestCase
{
    public function testDispatch()
    {
        $command = new TestCommand;
        $decoratedMock = $this->getMockBuilder(CompositeRequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedMock->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn('Success');

        DB::shouldReceive('beginTransaction')
            ->once();
        DB::shouldReceive('commit')
            ->once();

        $bus = new TransactionBus($decoratedMock);
        $this->assertEquals('Success', $bus->dispatch($command));
    }

    public function testDispatchWithException()
    {
        $this->expectException(\DomainException::class);
        $command = new TestCommand;
        $decoratedMock = $this->getMockBuilder(CompositeRequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedMock->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willThrowException(new \DomainException);

        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('rollBack')
            ->once();

        $bus = new TransactionBus($decoratedMock);
        $bus->dispatch($command);
    }

    public function testRegisterBus()
    {
        $registeredMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $decoratedMock = $this->getMockBuilder(CompositeRequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedMock->expects($this->once())
            ->method('registerBus')
            ->with($registeredMock);

        $bus = new TransactionBus($decoratedMock);
        $bus->registerBus($registeredMock);
    }

    public function testCanDispatch()
    {
        $command = new \stdClass;
        $decoratedMock = $this->getMockBuilder(CompositeRequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);

        $bus = new TransactionBus($decoratedMock);
        $this->assertTrue($bus->canDispatch($command));
    }

    public function testCantDispatch()
    {
        $command = new \stdClass;
        $decoratedMock = $this->getMockBuilder(CompositeRequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(false);

        $bus = new TransactionBus($decoratedMock);
        $this->assertFalse($bus->canDispatch($command));
    }
}