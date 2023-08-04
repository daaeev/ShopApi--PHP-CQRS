<?php

namespace Project\Modules\Catalogue\Settings\Infrastructure\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'catalogue_products_settings';
    public $timestamps = false;

    protected $fillable = [
        'product',
        'displayed',
    ];
}