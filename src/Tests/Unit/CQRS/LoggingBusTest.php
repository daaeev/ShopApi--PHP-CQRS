<?php

namespace Project\Tests\Unit\CQRS;

use Psr\Log\LoggerInterface;
use Project\Common\CQRS\Buses\LoggingBus;
use Project\Common\CQRS\Buses\Interfaces\AbstractCompositeBus;
use PHPUnit\Framework\TestCase;

class LoggingBusTest extends TestCase
{
    public function testDispatch()
    {
        $command = new Commands\TestCommand;
        $decoratedBusMock = $this->getMockBuilder(AbstractCompositeBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoratedBusMock->expects($this->once())
            ->method('dispatch')
            ->with($command);
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $message = 'Dispatch test: '
            . $command::class
            . ' with params '
            . json_encode(get_object_vars($command));
        $loggerMock->expects($this->once())
            ->method('info')
            ->with($message);

        $loggingBus = new LoggingBus($decoratedBusMock, $loggerMock, 'test');
        $loggingBus->dispatch($command);
    }
}