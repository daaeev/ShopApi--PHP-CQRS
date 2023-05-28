<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\IncludeRouteParams;

class ApiRequest extends FormRequest
{
    use IncludeRouteParams;
}