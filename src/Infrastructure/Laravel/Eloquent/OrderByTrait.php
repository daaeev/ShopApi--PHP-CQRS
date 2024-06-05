<?php

namespace Project\Infrastructure\Laravel\Eloquent;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait OrderByTrait
{
    public function scopeOrder(Builder $builder, array $sorting): void
    {
        foreach ($sorting as $column => $sort) {
            $builder->orderBy($column, $sort);
        }
    }
}