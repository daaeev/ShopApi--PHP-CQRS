<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\BaseApiController;

class CartController extends BaseApiController
{
    public function addItem(Requests\AddItem $request)
    {
        $this->dispatchCommand($request->getCommand());
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

    public function usePromocode(Requests\UsePromocode $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Promocode added');
    }

    public function removePromocode(Requests\RemovePromocode $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Promocode removed');
    }

    public function getActiveCart(Requests\GetActiveCart $request)
    {
        return $this->success($this->dispatchQuery($request->getQuery()));
    }
}