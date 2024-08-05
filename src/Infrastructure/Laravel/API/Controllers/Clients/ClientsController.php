<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Clients;

use Project\Infrastructure\Laravel\API\Controllers\BaseApiController;

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