<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    protected $table = 'clients_confirmations';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'client_id',
        'code',
        'expired_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}