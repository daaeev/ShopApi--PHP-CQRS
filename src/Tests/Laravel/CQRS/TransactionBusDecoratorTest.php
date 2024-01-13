<?php

namespace Project\Tests\Laravel\CQRS;

use Illuminate\Support\Facades\DB;
use Project\Common\ApplicationMessages\Buses\CompositeRequestBus;
use Project\Infrastructure\Laravel\ApplicationMessages\Buses\Decorators\TransactionBusDecorator;

class TransactionBusDecoratorTest extends \Tests\TestCase
{
    public function testCheckThatTransactionStartedAndCommitted()
    {
        $command = new \stdClass;
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
        $command = new \stdClass;
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