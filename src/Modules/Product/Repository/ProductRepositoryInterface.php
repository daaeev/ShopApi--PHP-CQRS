<?php

namespace Project\Modules\Product\Repository;

use Project\Modules\Product\Entity;

interface ProductRepositoryInterface
{
    public function add(Entity\Product $entity): void;

    public function update(Entity\Product $entity): void;

    public function delete(Entity\Product $entity): void;

    public function get(Entity\ProductId $id): Entity\Product;
}