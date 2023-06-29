<?php

namespace Project\Modules\Catalogue\Categories\Repository;

use Project\Modules\Catalogue\Categories\Entity;

interface CategoryRepositoryInterface
{
    public function add(Entity\Category $category): void;

    public function update(Entity\Category $delete): void;

    public function delete(Entity\CategoryId $id): void;

    public function get(Entity\CategoryId $id): Entity\Category;
}