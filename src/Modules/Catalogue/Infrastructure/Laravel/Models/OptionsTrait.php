<?php

namespace Project\Modules\Catalogue\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Builder;

trait OptionsTrait
{
    private array $options = [
        'active' => null,
        'displayed' => null,
    ];

    public function scopeOptions(Builder $query, array $options)
    {
        $mergedOptions = array_merge($this->options, $options);

        if (!empty($mergedOptions['active'])) {
            $query->where('active', $mergedOptions['active']);
        }

        if (!empty($mergedOptions['displayed'])) {
            $query->whereRelation('settings', function (Builder $query) use ($mergedOptions) {
                $query->where('displayed', $mergedOptions['displayed']);
            });
        }
    }
}