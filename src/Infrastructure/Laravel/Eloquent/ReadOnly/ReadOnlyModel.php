<?php

namespace Project\Infrastructure\Laravel\Eloquent\ReadOnly;

use Illuminate\Database\Eloquent\Model;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

class ReadOnlyModel extends Model
{
    use ReadOnlyTrait;
}