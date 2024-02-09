<?php

namespace Project\Modules\Catalogue\Product\Commands\Handlers;

use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Modules\Catalogue\Product\Commands\DeleteProductCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;

class DeleteProductHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private ProductsRepositoryInterface $products
    ) {}

    public function __invoke(DeleteProductCommand $command): void
    {
        $entity = $this->products->get(new ProductId($command->id));
        $entity->delete();
        $this->products->delete($entity);
        $this->dispatchEvents($entity->flushEvents());
    }
}