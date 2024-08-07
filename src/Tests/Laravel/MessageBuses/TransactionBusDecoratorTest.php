<?php

namespace Project\Tests\Laravel\MessageBuses;

use Illuminate\Support\Facades\DB;
use Project\Common\ApplicationMessages\Buses\CompositeRequestBus;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;
use Project\Infrastructure\Laravel\ApplicationMessages\Buses\Decorators\TransactionBusDecorator;

class TransactionBusDecoratorTest extends \Project\Tests\Laravel\TestCase
{
    public function testCheckThatTransactionStartedAndCommitted()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $decoratedMock = $this->getMockBuilder(CompositeRequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $decoratedMock->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn('Success');

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        $bus = new TransactionBusDecorator($decoratedMock);
        $this->assertEquals('Success', $bus->dispatch($command));
    }

    public function testCheckThatTransactionRollBackAfterException()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $decoratedMock = $this->getMockBuilder(CompositeRequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $decoratedMock->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willThrowException(new \DomainException);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();

        $bus = new TransactionBusDecorator($decoratedMock);
        $this->expectException(\DomainException::class);
        $bus->dispatch($command);
    }
}