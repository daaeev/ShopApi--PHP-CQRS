<?php

namespace App\Models\ReadOnly;

use Illuminate\Database\Eloquent\Model;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

class ReadOnlyModel extends Model
{
    use ReadOnlyTrait;
}