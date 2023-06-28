<?php

namespace Project\Modules\Catalogue\Content\Services;

use Project\Modules\Catalogue\Content\Commands\UpdateProductContentCommand;

interface ProductContentServiceInterface
{
    public function update(UpdateProductContentCommand $command): void;
}