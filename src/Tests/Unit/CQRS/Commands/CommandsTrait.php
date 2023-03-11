<?php

namespace Project\Tests\Unit\CQRS\Commands;

trait CommandsTrait
{
    private function getCommandBindings(): array
    {
        return [
            TestCommand::class => Handlers\CallableCommandHandler::class
        ];
    }
}