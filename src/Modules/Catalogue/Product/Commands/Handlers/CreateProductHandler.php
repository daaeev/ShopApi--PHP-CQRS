<?php

namespace Project\Modules\Catalogue\Product\Commands\Handlers;

use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Catalogue\Product\Entity;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Modules\Catalogue\Product\Commands\CreateProductCommand;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;

class CreateProductHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private ProductsRepositoryInterface $products,
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

        $command->active ? $entity->activate() : $entity->deactivate();
        $entity->setAvailability(Availability::from($command->availability));
        $entity->setSizes($command->sizes);
        $entity->setColors($command->colors);
        $this->products->add($entity);
        $this->dispatchEvents($entity->flushEvents());
        return $entity->getId()->getId();
    }
}