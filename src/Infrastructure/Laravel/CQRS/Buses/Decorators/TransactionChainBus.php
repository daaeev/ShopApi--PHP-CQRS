<?php

namespace Project\Infrastructure\Laravel\CQRS\Buses\Decorators;

use Illuminate\Support\Facades\DB;
use Project\Common\CQRS\Buses\Interfaces;
use Project\Common\CQRS\Buses\Interfaces\RequestBus;

class TransactionChainBus implements Interfaces\ChainBus
{
    private Interfaces\ChainBus $decorated;

    public function __construct(Interfaces\ChainBus $decorated)
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

    public function registerBus(RequestBus $bus): void
    {
        $this->decorated->registerBus($bus);
    }
}