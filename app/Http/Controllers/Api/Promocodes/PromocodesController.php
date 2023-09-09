<?php

namespace App\Http\Controllers\Api\Promocodes;

use App\Http\Controllers\BaseApiController;

class PromocodesController extends BaseApiController
{
    public function create(Requests\CreatePromocode $request)
    {
        $id = $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => $id], 'Promocode created');
    }

    public function update(Requests\UpdatePromocode $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Promocode updated');
    }

    public function delete(Requests\DeletePromocode $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Promocode deleted');
    }

    public function activate(Requests\ActivatePromocode $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Promocode activated');
    }

    public function deactivate(Requests\DeactivatePromocode $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Promocode deactivated');
    }

    public function get(Requests\GetPromocode $request)
    {
        return $this->success($this->dispatchQuery($request->getQuery()));
    }

    public function list(Requests\GetPromocodesList $request)
    {
        return $this->success($this->dispatchQuery($request->getQuery()));
    }
}