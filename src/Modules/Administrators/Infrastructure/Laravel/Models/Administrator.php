<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Hashing\Hasher;

class Administrator extends Model
{
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

        $this->password = $hasher->needsRehash($password)
            ? $hasher->make($password)
            : $password;
    }
}