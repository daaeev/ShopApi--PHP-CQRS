<?php

namespace Project\Infrastructure\Laravel\CQRS\Buses\Decorators;

use Illuminate\Support\Facades\DB;
use Project\Common\CQRS\Buses\Interfaces;

class TransactionBus extends Interfaces\AbstractCompositeBusDecorator
{
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
}