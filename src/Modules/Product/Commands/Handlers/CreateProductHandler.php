<?php

namespace Project\Modules\Product\Commands\Handlers;

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
        private ProductRepositoryInterface $products
    ) {}

    public function __invoke(CreateProductCommand $command): int
    {
        $entity = new Entity\Product(
            Entity\ProductId::next(),
            $command->name,
            $command->code,
            array_map(function (DTO\Price $price) {
                return new Entity\Price\Price(
                    $price->currency,
                    $price->price
                );
            }, $command->prices)
        );

        $command->active
            ? $entity->activate()
            : $entity->deactivate();

        $entity->setAvailability(Entity\Availability::from($command->availability));
        $entity->setSizes(array_map(function (string $size) {
            return Entity\Size\Size::from($size);
        }, $command->sizes));
        $entity->setColors(array_map(function (object $color) {
            return match ($color::class) {
                DTO\Colors\HexColor::class => new Entity\Color\HexColor($color->color)
            };
        }, $command->colors));

        $this->products->add($entity);
        $this->dispatchEvents($entity->flushEvents());
        return $entity->getId()->getId();
    }
}