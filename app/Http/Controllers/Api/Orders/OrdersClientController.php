<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\BaseApiController;
use Project\Common\Repository\NotFoundException;
use App\Http\Controllers\Api\Orders\Requests\Client as Requests;

class OrdersClientController extends BaseApiController
{
    public function create(Requests\CreateOrder $request)
    {
        $id = $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => $id], 'Order created');
    }

    public function get(Requests\GetOrder $request)
    {
        try {
            $data = $this->dispatchQuery($request->getQuery());
            return $this->success($data);
        } catch (NotFoundException) {
            return $this->error(404, 'Order does not exists');
        }
    }
}