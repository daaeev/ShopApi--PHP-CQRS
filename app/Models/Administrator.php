<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Project\Modules\Administrators\Infrastructure\Laravel\Models\Administrator as BaseAdmin;

class Administrator extends BaseAdmin implements Authenticatable
{
    use AuthenticatableTrait;
}