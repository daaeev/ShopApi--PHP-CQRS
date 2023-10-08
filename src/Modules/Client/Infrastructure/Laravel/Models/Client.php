<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

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
}