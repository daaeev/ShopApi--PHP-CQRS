<?php

namespace Project\Modules\Catalogue\Categories\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Categories\Entity\CategoryId;
use Project\Modules\Catalogue\Categories\Commands\UpdateCategoryCommand;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Catalogue\Categories\Repository\CategoryRepositoryInterface;

class UpdateCategoryHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CategoryRepositoryInterface $categories,
        private ProductRepositoryInterface $products
    ) {}

    public function __invoke(UpdateCategoryCommand $command): void
    {
        $category = $this->categories->get(new CategoryId($command->id));
        $category->updateName($command->name);
        $category->updateSlug($command->slug);

        if ($category->getParent()?->getId() !== $command->parent) {
            if ($command->parent !== null) {
                $this->categories->get(new CategoryId($command->parent));
                $category->attachParent(new CategoryId($command->parent));
            } else {
                $category->detachParent();
            }
        }

        if ($category->getProducts() !== $command->products) {
            $category->detachProducts();

            foreach ($command->products as $product) {
                $this->products->get(new ProductId($product));
                $category->attachProduct($product);
            }
        }

        $this->categories->update($category);
        $this->dispatchEvents($category->flushEvents());
    }
}