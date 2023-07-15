<?php

namespace App\Http\Controllers\Api\Catalogue;

use App\Http\Controllers\BaseApiController;

class CatalogueController extends BaseApiController
{
    public function details(Requests\CatalogueDetails $request)
    {
        return $this->success($this->dispatchQuery($request->getQuery()));
    }

    public function list(Requests\CatalogueList $request)
    {
        return $this->success($this->dispatchQuery($request->getQuery()));
    }
}