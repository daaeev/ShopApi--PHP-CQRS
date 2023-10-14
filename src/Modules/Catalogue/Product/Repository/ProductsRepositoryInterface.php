<?php

namespace Project\Modules\Catalogue\Product\Repository;

use Project\Modules\Catalogue\Product\Entity;

interface ProductsRepositoryInterface
{
    public function add(Entity\Product $entity): void;

    public function update(Entity\Product $entity): void;

    public function delete(Entity\Product $entity): void;

    public function get(Entity\ProductId $id): Entity\Product;
}