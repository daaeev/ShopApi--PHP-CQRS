<?php

namespace Project\Modules\Catalogue\Api;

use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Modules\Catalogue\Product\Repository\ProductApiRepositoryInterface;

class ProductApi
{
    private array $productsDTO = [];

    public function __construct(
        private ProductApiRepositoryInterface $products
    ) {}

    public function get(int $product): DTO\Product
    {
        if (!isset($this->productsDTO[$product])) {
            $this->fetchProduct($product);
        }

        return $this->productsDTO[$product];
    }

    private function fetchProduct(int $product): void
    {
        $productDTO = $this->products->get($product);
        $this->productsDTO[$productDTO->id] = $productDTO;
    }
}