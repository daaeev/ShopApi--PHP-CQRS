<?php

namespace Project\Modules\Catalogue\Product\Commands\Handlers;

use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Catalogue\Product\Entity;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Catalogue\Product\Commands\UpdateProductCommand;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;

class UpdateProductHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private ProductsRepositoryInterface $products,
    ) {}

    public function __invoke(UpdateProductCommand $command): void
    {
        $entity = $this->products->get(new Entity\ProductId($command->id));
        $entity->setName($command->name);
        $entity->setCode($command->code);
        $entity->setPrices(array_map(function (DTO\Price $price) {
            return new Entity\Price\Price(
                Currency::from($price->currency),
                $price->price
            );
        }, $command->prices));

        $command->active ? $entity->activate() : $entity->deactivate();
        $entity->setAvailability(Availability::from($command->availability));
        $entity->setSizes($command->sizes);
        $entity->setColors($command->colors);
        $this->products->update($entity);
        $this->dispatchEvents($entity->flushEvents());
    }
}