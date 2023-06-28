<?php

namespace App\Http\Controllers\Api\Catalogue;

use App\Http\Controllers\BaseApiController;
use App\Http\Controllers\Api\Catalogue\Content\Requests;

class ContentController extends BaseApiController
{
    public function update(Requests\UpdateContent $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Content updated');
    }
}