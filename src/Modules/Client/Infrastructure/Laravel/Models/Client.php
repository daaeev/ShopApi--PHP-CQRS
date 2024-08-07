<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Project\Infrastructure\Laravel\Eloquent\ReadOnly\Cart;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'id',
        'firstname',
        'lastname',
        'phone',
        'email',
        'phone_confirmed',
        'email_confirmed',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'phone_confirmed' => 'boolean',
        'email_confirmed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->hasOne(Cart::class, 'client_id');
    }

    public function scopeHasNotEmptyCart(Builder $query, bool $hasNotEmptyCart)
    {
        if (!$hasNotEmptyCart) {
            return;
        }

        $query->whereHas('cart.items');
    }
}