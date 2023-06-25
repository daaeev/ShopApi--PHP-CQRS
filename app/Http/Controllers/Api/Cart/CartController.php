<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\BaseApiController;

class CartController extends BaseApiController
{
    public function addItem(Requests\AddItem $request)
    {
        $id = $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Cart item added');
    }

    public function updateItem(Requests\UpdateItem $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Cart item updated');
    }

    public function removeItem(Requests\RemoveItem $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Cart item removed');
    }

    public function changeCartCurrency(Requests\ChangeCartCurrency $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Currency changed');
    }

    public function getActiveCart(Requests\GetActiveCart $request)
    {
        $data = $this->dispatchQuery($request->getQuery());
        return $this->success($data);
    }
}