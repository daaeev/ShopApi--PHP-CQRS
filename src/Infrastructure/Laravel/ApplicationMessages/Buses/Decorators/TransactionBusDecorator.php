<?php

namespace Project\Infrastructure\Laravel\ApplicationMessages\Buses\Decorators;

use Illuminate\Support\Facades\DB;
use Project\Common\ApplicationMessages\Buses\Decorators\AbstractCompositeMessageBusDecorator;

class TransactionBusDecorator extends AbstractCompositeMessageBusDecorator
{
    public function dispatch(object $request): mixed
    {
        DB::beginTransaction();

        try {
            $result = $this->decorated->dispatch($request);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $result;
    }
}