<?php

namespace Project\Tests\Laravel\CQRS;

use Illuminate\Support\Facades\DB;
use Project\Common\CQRS\Buses\CompositeBus;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Infrastructure\Laravel\CQRS\Buses\Decorators\TransactionCompositeBus;
use Project\Tests\Unit\CQRS\Commands\TestCommand;

class TransactionCompositeBusTest extends \Tests\TestCase
{
    public function testDispatch()
    {
        $command = new TestCommand;
        $decoratedMock = $this->getMockBuilder(CompositeBus::class)
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

        $bus = new TransactionCompositeBus($decoratedMock);
        $this->assertEquals('Success', $bus->dispatch($command));
    }

    public function testDispatchWithException()
    {
        $this->expectException(\DomainException::class);
        $command = new TestCommand;
        $decoratedMock = $this->getMockBuilder(CompositeBus::class)
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

        $bus = new TransactionCompositeBus($decoratedMock);
        $bus->dispatch($command);
    }

    public function testRegisterBus()
    {
        $registeredMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $decoratedMock = $this->getMockBuilder(CompositeBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedMock->expects($this->once())
            ->method('registerBus')
            ->with($registeredMock);

        $bus = new TransactionCompositeBus($decoratedMock);
        $bus->registerBus($registeredMock);
    }

    public function testCanDispatch()
    {
        $command = new \stdClass;
        $decoratedMock = $this->getMockBuilder(CompositeBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);

        $bus = new TransactionCompositeBus($decoratedMock);
        $this->assertTrue($bus->canDispatch($command));
    }

    public function testCantDispatch()
    {
        $command = new \stdClass;
        $decoratedMock = $this->getMockBuilder(CompositeBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(false);

        $bus = new TransactionCompositeBus($decoratedMock);
        $this->assertFalse($bus->canDispatch($command));
    }
}