<?php

namespace Project\Infrastructure\Laravel\ApplicationMessages\Buses\Decorators;

use Illuminate\Support\Facades\DB;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;
use Project\Common\ApplicationMessages\Buses\Decorators\AbstractCompositeMessageBusDecorator;

class TransactionBusDecorator extends AbstractCompositeMessageBusDecorator
{
    public function dispatch(ApplicationMessageInterface $message): mixed
    {
        DB::beginTransaction();

        try {
            $result = parent::dispatch($message);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $result;
    }
}