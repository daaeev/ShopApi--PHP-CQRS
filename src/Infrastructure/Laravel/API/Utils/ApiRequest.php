<?php

namespace Project\Infrastructure\Laravel\API\Utils;

use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    use IncludeRouteParams;
}