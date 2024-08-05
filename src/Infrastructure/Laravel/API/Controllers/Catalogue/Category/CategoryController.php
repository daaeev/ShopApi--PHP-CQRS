<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Category;

use Project\Infrastructure\Laravel\API\Controllers\BaseApiController;

class CategoryController extends BaseApiController
{
    public function create(Requests\CreateCategory $request)
    {
        $id = $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $id], 'Category created');
    }

    public function update(Requests\UpdateCategory $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Category updated');
    }

    public function delete(Requests\DeleteCategory $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Category deleted');
    }

    public function get(Requests\GetCategory $request)
    {
        $data = $this->dispatchQuery($request->getQuery());
        return $this->success($data);
    }

    public function list(Requests\CategoriesList $request)
    {
        $data = $this->dispatchQuery($request->getQuery());
        return $this->success($data);
    }
}