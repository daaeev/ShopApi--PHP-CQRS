<?php

namespace Project\Modules\Catalogue\Product\Commands\Handlers;

use Project\Modules\Catalogue\Product\Entity;
use Project\Common\Product\Currency;
use Project\Modules\Catalogue\Product\Api\DTO;
use Project\Common\Product\Availability;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Product\Commands\CreateProductCommand;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;

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
        $entity->setAvailability(Availability::from($command->availability));
        $entity->setSizes($command->sizes);
        $entity->setColors($command->colors);

        $this->products->add($entity);
        $this->dispatchEvents($entity->flushEvents());
        return $entity->getId()->getId();
    }
}