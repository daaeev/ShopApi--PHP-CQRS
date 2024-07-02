<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\BaseApiController;
use Project\Common\Repository\NotFoundException;

class OrdersAdminController extends BaseApiController
{
    public function update(Requests\UpdateOrder $request)
    {
        try {
            $this->dispatchCommand($request->getCommand());
            return $this->success(['id' => (int) $request->get('id')], 'Order updated');
        } catch (NotFoundException) {
            return $this->error(404, 'Order does not exists');
        }
    }

    public function delete(Requests\DeleteOrder $request)
    {
        try {
            $this->dispatchCommand($request->getCommand());
            return $this->success(['id' => (int) $request->get('id')], 'Order deleted');
        } catch (NotFoundException) {
            return $this->error(404, 'Order does not exists');
        }
    }

    public function addOffer(Requests\AddOffer $request)
    {
        try {
            $this->dispatchCommand($request->getCommand());
            return $this->success(['id' => (int) $request->get('id')], 'Offer added');
        } catch (NotFoundException) {
            return $this->error(404, 'Order does not exists');
        }
    }

    public function updateOffer(Requests\UpdateOffer $request)
    {
        try {
            $this->dispatchCommand($request->getCommand());
            return $this->success(['id' => (int) $request->get('id')], 'Offer updated');
        } catch (NotFoundException) {
            return $this->error(404, 'Order does not exists');
        }
    }

    public function removeOffer(Requests\RemoveOffer $request)
    {
        try {
            $this->dispatchCommand($request->getCommand());
            return $this->success(['id' => (int) $request->get('id')], 'Offer removed');
        } catch (NotFoundException) {
            return $this->error(404, 'Order does not exists');
        }
    }

    public function addPromo(Requests\AddPromo $request)
    {
        try {
            $this->dispatchCommand($request->getCommand());
            return $this->success(['id' => (int) $request->get('id')], 'Promo added');
        } catch (NotFoundException) {
            return $this->error(404, 'Order does not exists');
        }
    }

    public function removePromo(Requests\RemovePromo $request)
    {
        try {
            $this->dispatchCommand($request->getCommand());
            return $this->success(['id' => (int) $request->get('id')], 'Promo removed');
        } catch (NotFoundException) {
            return $this->error(404, 'Order does not exists');
        }
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

    public function list(Requests\GetOrders $request)
    {
        $data = $this->dispatchQuery($request->getQuery());
        return $this->success($data);
    }
}