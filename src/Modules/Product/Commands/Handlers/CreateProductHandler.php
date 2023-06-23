<?php

namespace Project\Modules\Product\Commands\Handlers;

use Project\Common\Currency;
use Project\Modules\Product\Entity;
use Project\Modules\Product\Api\DTO;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Product\Commands\CreateProductCommand;
use Project\Modules\Product\Repository\ProductRepositoryInterface;

class CreateProductHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private ProductRepositoryInterface $products,
    ) {}

    public function __invoke(CreateProductCommand $command): int
    {
        $entity = new Entity\Product(
            Entity\ProductId::next(),
            $command->name,
            $command->code,
            array_map(function (DTO\Price $price) {
                return new Entity\Price\Price(
                    Currency::from($price->currency),
                    $price->price
                );
            }, $command->prices)
        );
        $command->active
            ? $entity->activate()
            : $entity->deactivate();
        $entity->setAvailability(Entity\Availability::from($command->availability));
        $entity->setSizes($command->sizes);
        $entity->setColors($command->colors);

        $this->products->add($entity);
        $this->dispatchEvents($entity->flushEvents());
        return $entity->getId()->getId();
    }
}