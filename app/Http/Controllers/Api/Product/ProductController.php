<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\BaseApiController;

class ProductController extends BaseApiController
{
    public function create(Requests\CreateProduct $request)
    {
        $id = $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $id], 'Product created');
    }

    public function update(Requests\UpdateProduct $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Product updated');
    }

    public function delete(Requests\DeleteProduct $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Product deleted');
    }

    public function get(Requests\GetProduct $request)
    {
        $data = $this->dispatchQuery($request->getQuery());
        return $this->success($data);
    }

    public function list(Requests\ProductsList $request)
    {
        $data = $this->dispatchQuery($request->getQuery());
        return $this->success($data);
    }
}