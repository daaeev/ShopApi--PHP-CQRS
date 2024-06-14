<?php

namespace Project\Infrastructure\Laravel\Eloquent;

use Illuminate\Support\Str;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait OrderByTrait
{
    public function scopeOrder(Builder $builder, array $sorting): void
    {
        foreach ($sorting as $column => $sort) {
            $builder->orderBy(Str::snake($column), $sort);
        }
    }
}