<?php

namespace Project\Modules\Catalogue\Settings\Commands;

class UpdateProductSettingsCommand
{
    public function __construct(
        public readonly int $product,
        public readonly bool $displayed,
    ) {}
}