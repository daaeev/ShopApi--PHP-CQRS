<?php

namespace Project\Modules\Product\Commands\Handlers;

use Project\Common\Currency;
use Project\Modules\Product\Entity;
use Project\Modules\Product\Api\DTO;
use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Product\Commands\UpdateProductCommand;
use Project\Modules\Product\Repository\ProductRepositoryInterface;

class UpdateProductHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private ProductRepositoryInterface $products,
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
        $command->active
            ? $entity->activate()
            : $entity->deactivate();
        $entity->setAvailability(Entity\Availability::from($command->availability));
        $entity->setSizes(array_map(function (string $size) {
            return new Entity\Size\Size($size);
        }, $command->sizes));
        $entity->setColors(array_map(function (DTO\Color $color) {
            return Entity\Color\ColorTypeMapper::makeByType($color->type, $color->color);
        }, $command->colors));

        $this->products->update($entity);
        $this->dispatchEvents($entity->flushEvents());
    }
}