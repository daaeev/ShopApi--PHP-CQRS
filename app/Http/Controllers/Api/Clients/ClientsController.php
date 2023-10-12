<?php

namespace App\Http\Controllers\Api\Clients;

use App\Http\Controllers\BaseApiController;

class ClientsController extends BaseApiController
{
    public function get(Requests\GetClient $request)
    {
        return $this->success($this->dispatchQuery($request->getQuery()));
    }

    public function list(Requests\GetClients $request)
    {
        return $this->success($this->dispatchQuery($request->getQuery()));
    }
}