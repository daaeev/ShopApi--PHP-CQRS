<?php

namespace App\Http\Controllers;

use App\Http\Utils\ApiResponser;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Project\Infrastructure\Laravel\ApplicationMessages\DispatchMessagesTrait;

class BaseApiController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponser, DispatchMessagesTrait;
}
