<?php

namespace Project\Modules\Catalogue\Categories\Repository;

use Project\Modules\Catalogue\Categories\Entity;

interface CategoriesRepositoryInterface
{
    public function add(Entity\Category $entity): void;

    public function update(Entity\Category $entity): void;

    public function delete(Entity\Category $entity): void;

    public function get(Entity\CategoryId $id): Entity\Category;
}