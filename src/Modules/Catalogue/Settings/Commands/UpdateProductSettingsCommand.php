<?php

namespace Project\Modules\Catalogue\Settings\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UpdateProductSettingsCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $product,
        public readonly bool $displayed,
    ) {}
}