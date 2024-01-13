<?php

namespace App\Http\Controllers;

use App\Http\Utils\ApiResponser;
use App\Http\Utils\DispatchRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Project\Common\ApplicationMessages\ApplicationMessagesManager;

class BaseApiController extends BaseController
{
    use AuthorizesRequests,
        ValidatesRequests,
        ApiResponser,
        DispatchRequests;

    public function __construct(
        ApplicationMessagesManager $messagesManager
    ) {
        $this->messagesManager = $messagesManager;
    }
}
