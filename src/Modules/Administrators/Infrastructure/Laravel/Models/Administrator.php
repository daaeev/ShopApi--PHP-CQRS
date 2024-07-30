<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Administrator extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $fillable = [
        'id',
        'name',
        'login',
        'password',
        'roles'
    ];

    protected $casts = [
        'roles' => 'array'
    ];

    public function hashPassword(Hasher $hasher, string $password): void
    {
        if (empty($password)) {
            throw new \DomainException('Administrator password cant be empty');
        }

        if ($hasher->check($password, $this->password)) {
            return;
        }

        $this->password = $hasher->needsRehash($password) ? $hasher->make($password) : $password;
    }
}