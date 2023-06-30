<?php

namespace App\Http\Controllers\Api\Catalogue\CategoryContent;

use App\Http\Controllers\BaseApiController;

class ContentController extends BaseApiController
{
    public function updateContent(Requests\UpdateContent $request)
    {
        $id = $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $id], 'Content updated');
    }
}