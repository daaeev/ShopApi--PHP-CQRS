<?php

namespace App\Models;

use MichaelAChrisco\ReadOnly\ReadOnlyTrait;
use Illuminate\Database\Eloquent\Model;

class ReadOnlyModel extends Model
{
    use ReadOnlyTrait;
}