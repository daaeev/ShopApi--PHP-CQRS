<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Catalogue\Settings;

use Project\Infrastructure\Laravel\API\Controllers\BaseApiController;

class SettingsController extends BaseApiController
{
    public function update(Requests\UpdateSettings $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => (int) $request->get('id')], 'Settings updated');
    }
}