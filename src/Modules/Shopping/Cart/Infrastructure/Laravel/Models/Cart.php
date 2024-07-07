<?php

namespace Project\Modules\Shopping\Cart\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Cart extends Model
{
    protected $table = 'shopping_carts';

    protected $fillable = [
        'id',
        'client_hash',
        'client_id',
        'currency',
        'total_price',
        'regular_price',
        'promocode',
        'promocode_id',
        'promocode_discount_percent',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }

    public function scopeClient(Builder $query, int|string|null $id, string|null $hash): void
    {
        if ($id && $hash) {
            $query->where(fn ($subQuery) => $subQuery->where('client_id', $id)->orWhere('client_hash', $hash));
        } else if ($id) {
            $query->where('client_id', $id);
        } else if ($hash) {
            $query->where('client_hash', $hash);
        }
    }
}