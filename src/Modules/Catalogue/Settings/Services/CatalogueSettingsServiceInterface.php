<?php

namespace Project\Modules\Catalogue\Settings\Services;

use Project\Modules\Catalogue\Settings\Commands\UpdateProductSettingsCommand;

interface CatalogueSettingsServiceInterface
{
    public function update(UpdateProductSettingsCommand $command): void;
}