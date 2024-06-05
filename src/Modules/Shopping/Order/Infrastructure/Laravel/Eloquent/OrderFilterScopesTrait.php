<?php

namespace Project\Modules\Shopping\Order\Infrastructure\Laravel\Eloquent;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait OrderFilterScopesTrait
{
    public function scopePrice(Builder $builder, int|float|null $from, int|float|null $to): void
    {
        if (null !== $from) {
            $builder->where('total_price', '>=', $from);
        }

        if (null !== $to) {
            $builder->where('total_price', '<=', $to);
        }
    }

    public function scopeClient(Builder $builder, int|string|null $id): void
    {
        if (null !== $id) {
            $builder->where('client_id', $id);
        }
    }

    public function scopePhone(Builder $builder, string|null $phone): void
    {
        if (null !== $phone) {
            $builder->where('phone', $phone);
        }
    }

    public function scopeEmail(Builder $builder, string|null $email): void
    {
        if (null !== $email) {
            $builder->where('email', $email);
        }
    }

    public function scopeName(Builder $builder, string|null $name): void
    {
        if (null !== $name) {
            $builder->where(
                DB::raw("CONCAT(`first_name`, ' ', `last_name`)"),
                'LIKE',
                "%$name%"
            );
        }
    }

    public function scopeStatus(Builder $builder, string|null $status): void
    {
        if (null !== $status) {
            $builder->where('status', $status);
        }
    }

    public function scopePaymentStatus(Builder $builder, string|null $paymentStatus): void
    {
        if (null !== $paymentStatus) {
            $builder->where('payment_status', $paymentStatus);
        }
    }
}