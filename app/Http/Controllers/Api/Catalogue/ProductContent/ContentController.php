<?php

namespace App\Http\Controllers\Api\Catalogue\ProductContent;

use App\Http\Controllers\BaseApiController;

class ContentController extends BaseApiController
{
    public function updateContent(Requests\UpdateContent $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Content updated');
    }

    public function updatePreview(Requests\UpdatePreview $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Preview updated');
    }

    public function addImage(Requests\AddImage $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Image added');
    }

    public function deleteImage(Requests\DeleteImage $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Image deleted');
    }
}