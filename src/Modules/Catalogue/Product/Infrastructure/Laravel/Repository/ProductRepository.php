<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository;

use Project\Common\Utils\DateTimeFormat;
use Project\Modules\Catalogue\Product\Entity;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models as Eloquent;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
    ) {}

    public function add(Entity\Product $entity): void
    {
        $id = $entity->getId()->getId();

        if (Eloquent\Product::find($id)) {
            throw new DuplicateKeyException('Product with same id already exists');
        }

        $this->persist($entity, new Eloquent\Product);
    }

    private function persist(Entity\Product $entity, Eloquent\Product $record): void
    {
        $this->guardCodeIsUnique($entity);

        if (!$record->exists) {
            $record->id = $entity->getId()->getId();
            $record->created_at = $entity->getCreatedAt()->format(DateTimeFormat::FULL_DATE->value);
        }

        $record->name = $entity->getName();
        $record->code = $entity->getCode();
        $record->active = $entity->isActive();
        $record->availability = $entity->getAvailability()->value;
        $record->updated_at = $entity->getUpdatedAt()?->format(DateTimeFormat::FULL_DATE->value);
        $record->save();
        $this->hydrator->hydrate($entity->getId(), ['id' => $record->id]);
        $this->persistSizes($entity, $record);
        $this->persistColors($entity, $record);
        $this->persistPrices($entity, $record);
    }

    private function guardCodeIsUnique(Entity\Product $entity): void
    {
        $code = $entity->getCode();

        $notUnique = Eloquent\Product::query()
            ->where('code', $code)
            ->where('id', '!=', $entity->getId()->getId())
            ->exists();

        if ($notUnique) {
            throw new DuplicateKeyException('Product with same code already exists');
        }
    }

    private function persistSizes(Entity\Product $entity, Eloquent\Product $record): void
    {
        $record->sizes()->delete();

        foreach ($entity->getSizes() as $size) {
            $record->sizes()->create([
                'size' => $size
            ]);
        }
    }

    private function persistColors(Entity\Product $entity, Eloquent\Product $record): void
    {
        $record->colors()->delete();

        foreach ($entity->getColors() as $color) {
            $record->colors()->create([
                'color' => $color,
            ]);
        }
    }

    private function persistPrices(Entity\Product $entity, Eloquent\Product $record): void
    {
        $record->prices()->delete();

        foreach ($entity->getPrices() as $price) {
            $record->prices()->create([
                'currency' => $price->getCurrency(),
                'price' => $price->getPrice()
            ]);
        }
    }

    public function update(Entity\Product $entity): void
    {
        $id = $entity->getId()->getId();

        if (!$record = Eloquent\Product::find($id)) {
            throw new NotFoundException('Product does not exists');
        }

        $this->persist($entity, $record);
    }

    public function delete(Entity\Product $entity): void
    {
        $id = $entity->getId()->getId();

        if (!$record = Eloquent\Product::find($id)) {
            throw new NotFoundException('Product does not exists');
        }

        $record->delete();
    }

    public function get(Entity\ProductId $id): Entity\Product
    {
        if (!$record = Eloquent\Product::find($id->getId())) {
            throw new NotFoundException('Product does not exists');
        }

        return $this->hydrate($record);
    }

    private function hydrate(Eloquent\Product $record): Entity\Product
    {
        return $this->hydrator->hydrate(Entity\Product::class, [
            'id' => new Entity\ProductId($record->id),
            'name' => $record->name,
            'code' => $record->code,
            'active' => $record->active,
            'availability' => Availability::from($record->availability),
            'colors' => $this->hydrateColors($record),
            'sizes' => $this->hydrateSizes($record),
            'prices' => $this->hydratePrices($record),
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        ]);
    }

    private function hydrateColors(Eloquent\Product $record): array
    {
        return array_column($record->colors->all(), 'color');
    }

    private function hydrateSizes(Eloquent\Product $record): array
    {
        return array_column($record->sizes->all(), 'size');
    }

    private function hydratePrices(Eloquent\Product $record): array
    {
        $hydratedPrices = [];

        foreach ($record->prices as $price) {
            $hydratedPrices[$price->currency] = new Entity\Price\Price(
                Currency::from($price->currency),
                $price->price
            );
        }

        return $hydratedPrices;
    }
}