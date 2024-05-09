<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\BaseApiController;

class CartController extends BaseApiController
{
    public function addOffer(Requests\AddOffer $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Offer added');
    }

    public function updateOffer(Requests\UpdateOffer $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Offer updated');
    }

    public function removeOffer(Requests\RemoveOffer $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'Offer removed');
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

    public function getActiveCart(Requests\GetCart $request)
    {
        return $this->success($this->dispatchQuery($request->getQuery()));
    }
}