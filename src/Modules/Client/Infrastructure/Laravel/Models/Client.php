<?php

namespace Project\Modules\Client\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Project\Modules\Client\Entity\Access as Entity;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Client extends Model implements Authenticatable
{
    use AuthenticatableTrait;

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

    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class, 'client_id', 'id');
    }

    public function scopeHasAccess(Builder $query, Entity\Access $access): void
    {
        $query->whereHas('accesses', function (Builder $accessesQuery) use ($access) {
            $accessesQuery->where('credentials', $access->getCredentials());
        });
    }
}