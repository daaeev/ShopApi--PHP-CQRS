<?php

namespace Project\Modules\Catalogue\Product\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Product\Commands\DeleteProductCommand;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;

class DeleteProductHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private ProductRepositoryInterface $products
    ) {}

    public function __invoke(DeleteProductCommand $command): void
    {
        $entity = $this->products->get(new ProductId($command->id));
        $entity->delete();
        $this->products->delete($entity);
        $this->dispatchEvents($entity->flushEvents());
    }
}