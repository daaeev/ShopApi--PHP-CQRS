<?php

namespace Project\Modules\Product\Commands\Handlers;

use Project\Modules\Product\Entity\ProductId;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Product\Commands\DeleteProductCommand;
use Project\Modules\Product\Repository\ProductRepositoryInterface;

class DeleteProductHandlers implements DispatchEventsInterface
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