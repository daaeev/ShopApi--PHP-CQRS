<?php

namespace Project\Modules\Catalogue\Api\DTO;

use Webmozart\Assert\Assert;
use Project\Common\Utils\DTO;

class CatalogueProduct implements DTO
{
    public function __construct(
        public readonly Product\Product $product,
        public readonly Product\Content $content,
        public readonly ?string $preview,
        public readonly array $additionalImages,
        public readonly Product\Settings $settings,
        public readonly array $categories
    ) {
        Assert::allIsInstanceOf($this->categories, CatalogueCategory::class);
        Assert::allString($this->additionalImages);
    }

    public function toArray(): array
    {
        return [
            ...$this->product->toArray(),
            'content' => $this->content->toArray(),
            'images' => [
                'preview' => $this->preview,
                'additional' => $this->additionalImages,
            ],
            'settings' => $this->settings->toArray(),
            'categories' => array_map(function (CatalogueCategory $category) {
                return $category->toArray();
            }, $this->categories)
        ];
    }
}