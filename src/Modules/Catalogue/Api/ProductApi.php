<?php

namespace Project\Modules\Catalogue\Api;

use Project\Modules\Catalogue\Product\Api\DTO;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Product\Utils\Entity2DTOConverter;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;

class ProductApi
{
    private array $productsDTO = [];

    public function __construct(
        private ProductRepositoryInterface $products
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
        $productDTO = Entity2DTOConverter::convert(
            $this->products->get(new ProductId($product))
        );

        $this->productsDTO[$productDTO->id] = $productDTO;
    }
}