<?php

namespace Project\Modules\Catalogue\Categories\Commands\Handlers;

use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Categories\Entity\Category;
use Project\Modules\Catalogue\Categories\Entity\CategoryId;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Categories\Commands\CreateCategoryCommand;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;
use Project\Modules\Catalogue\Categories\Repository\CategoriesRepositoryInterface;

class CreateCategoryHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CategoriesRepositoryInterface $categories,
        private ProductsRepositoryInterface $products
    ) {}

    public function __invoke(CreateCategoryCommand $command): int
    {
        $category = new Category(
            CategoryId::next(),
            $command->name,
            $command->slug,
        );

        if (!empty($command->parent)) {
            $this->categories->get(new CategoryId($command->parent));
            $category->attachParent(new CategoryId($command->parent));
        }

        if (!empty($command->products)) {
            foreach ($command->products as $product) {
                $this->products->get(new ProductId($product));
                $category->attachProduct($product);
            }
        }

        $this->categories->add($category);
        $this->dispatchEvents($category->flushEvents());
        return $category->getId()->getId();
    }
}