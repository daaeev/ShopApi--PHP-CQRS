<?php

namespace Project\Infrastructure\Laravel\CQRS\Buses\Decorators;

use Illuminate\Support\Facades\DB;
use Project\Common\CQRS\Buses\Interfaces;

class TransactionCompositeBus extends Interfaces\AbstractCompositeBus
{
    private Interfaces\AbstractCompositeBus $decorated;

    public function __construct(Interfaces\AbstractCompositeBus $decorated)
    {
        $this->decorated = $decorated;
    }

    public function dispatch(object $command): mixed
    {
        DB::beginTransaction();

        try {
            $result = $this->decorated->dispatch($command);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $result;
    }

    public function canDispatch($command): bool
    {
        return $this->decorated->canDispatch($command);
    }

    public function registerBus(Interfaces\RequestBus $bus): void
    {
        $this->decorated->registerBus($bus);
    }
}