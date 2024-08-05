<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\CategoryContent;

use Project\Infrastructure\Laravel\API\Controllers\BaseApiController;

class ContentController extends BaseApiController
{
    public function updateContent(Requests\UpdateContent $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Content updated');
    }
}