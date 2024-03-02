<?php

namespace Project\Modules\Catalogue\Product\Infrastructure\Laravel\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Modules\Catalogue\Product\Entity;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;
use Project\Modules\Catalogue\Product\Infrastructure\Laravel\Models as Eloquent;

class ProductsEloquentRepository implements ProductsRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
        private IdentityMap $identityMap,
    ) {}

    public function add(Entity\Product $entity): void
    {
        $id = $entity->getId()->getId();
        if (!empty($id) && $this->identityMap->has($id)) {
            throw new DuplicateKeyException('Product with same id already exists');
        }

        if (Eloquent\Product::find($id)) {
            throw new DuplicateKeyException('Product with same id already exists');
        }

        $this->persist($entity, new Eloquent\Product);
        $this->identityMap->add($entity->getId()->getId(), $entity);
    }

    private function persist(Entity\Product $entity, Eloquent\Product $record): void
    {
        $this->guardCodeIsUnique($entity);

        $record->id = $entity->getId()->getId();
        $record->name = $entity->getName();
        $record->code = $entity->getCode();
        $record->active = $entity->isActive();
        $record->availability = $entity->getAvailability()->value;
        $record->created_at = $entity->getCreatedAt()->getTimestamp();
        $record->updated_at = $entity->getUpdatedAt()?->getTimestamp();
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
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Product does not exists');
        }

        if (!$record = Eloquent\Product::find($id)) {
            throw new NotFoundException('Product does not exists');
        }

        $this->persist($entity, $record);
    }

    public function delete(Entity\Product $entity): void
    {
        $id = $entity->getId()->getId();
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Product does not exists');
        }

        if (!$record = Eloquent\Product::find($id)) {
            throw new NotFoundException('Product does not exists');
        }

        $this->identityMap->remove($id);
        $record->delete();
    }

    public function get(Entity\ProductId $id): Entity\Product
    {
        if (empty($id->getId())) {
            throw new NotFoundException('Product does not exists');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        if (!$record = Eloquent\Product::find($id->getId())) {
            throw new NotFoundException('Product does not exists');
        }

        $entity = $this->hydrate($record);
        $this->identityMap->add($id->getId(), $entity);
        return $entity;
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
