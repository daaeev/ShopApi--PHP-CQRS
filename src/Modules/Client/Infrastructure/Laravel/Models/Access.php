<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Project\Modules\Client\Entity\Access\AccessType;

class Access extends Model
{
    protected $table = 'clients_accesses';

    protected $fillable = [
        'id',
        'client_id',
        'type',
        'credentials',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'type' => AccessType::class,
        'credentials' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}