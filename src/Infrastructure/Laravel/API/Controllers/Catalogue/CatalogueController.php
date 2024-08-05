<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue;

use Project\Infrastructure\Laravel\API\Controllers\BaseApiController;

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