<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Client extends Model
{
    protected $table = 'clients';
    protected $fillable = [
        'hash',
        'firstname',
        'lastname',
        'phone',
        'email',
        'phone_confirmed',
        'email_confirmed',
    ];
    protected $casts = [
        'phone_confirmed' => 'boolean',
        'email_confirmed' => 'boolean',
    ];

    public function carts()
    {
        return $this->hasMany(ReadOnly\Cart::class, 'client_id');
    }

    public function scopeApplyOptions(Builder $query, array $options)
    {
        if (!empty($options['hasNotEmptyCart'])) {
            $query->whereHas('carts', function (Builder $query) {
                $query->where('active', true)
                    ->has('items');
            });
        }
    }
}