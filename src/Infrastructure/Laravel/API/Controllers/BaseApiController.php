<?php

namespace Project\Infrastructure\Laravel\API\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Project\Infrastructure\Laravel\API\Utils\ApiResponser;
use Project\Infrastructure\Laravel\ApplicationMessages\DispatchMessagesTrait;

class BaseApiController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponser, DispatchMessagesTrait;
}
