<?php

namespace App\Http\Controllers\Api\Catalogue\Settings;

use App\Http\Controllers\BaseApiController;

class SettingsController extends BaseApiController
{
    public function update(Requests\UpdateSettings $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Settings updated');
    }
}