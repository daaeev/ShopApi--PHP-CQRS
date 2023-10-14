<?php

namespace Project\Modules\Catalogue\Categories\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Categories\Entity\CategoryId;
use Project\Modules\Catalogue\Categories\Commands\DeleteCategoryCommand;
use Project\Modules\Catalogue\Categories\Repository\CategoriesRepositoryInterface;

class DeleteCategoryHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private CategoriesRepositoryInterface $categories,
    ) {}

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $category = $this->categories->get(new CategoryId($command->id));
        $category->delete();
        $this->categories->delete($category);
        $this->dispatchEvents($category->flushEvents());
    }
}