<?php

namespace Project\Modules\Test\Infrastructure\Laravel;

use Project\Common\CQRS\Buses\EventBus;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Modules\Test\Events\Consumers\TestEventConsumer;
use Project\Modules\Test\Events\TestEvent;
use Project\Modules\Test\Requests\Commands\Handlers\TestCommandHandler;
use Project\Modules\Test\Requests\Commands\TestCommand;

class TestServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private array $commandsMapping = [
        TestCommand::class => TestCommandHandler::class
    ];

    private array $queriesMapping = [];

    private array $eventsMapping = [
        TestEvent::class => [
            TestEventConsumer::class,
            TestEventConsumer::class,
        ]
    ];

    public function boot()
    {
        $this->app->make('CommandBus')->registerBus(new RequestBus($this->commandsMapping, $this->app));
        $this->app->make('EventBus')->registerBus(new EventBus($this->eventsMapping, $this->app));
    }
}