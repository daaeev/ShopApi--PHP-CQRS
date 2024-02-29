<?php

namespace Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\IdentityMap;
use Project\Modules\Catalogue\Categories\Entity;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Catalogue\Categories\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Catalogue\Categories\Repository\CategoriesRepositoryInterface;

class CategoriesEloquentRepository implements CategoriesRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
        private IdentityMap $identityMap,
    ) {}

    public function add(Entity\Category $entity): void
    {
        $id = $entity->getId()->getId();
        if (!empty($id) && $this->identityMap->has($id)) {
            throw new DuplicateKeyException('Category with same id already exists');
        }

        if (Eloquent\Category::find($id)) {
            throw new DuplicateKeyException('Category with same id already exists');
        }

        $this->persist($entity, new Eloquent\Category());
        $this->identityMap->add($entity->getId()->getId(), $entity);
    }

    private function persist(Entity\Category $entity, Eloquent\Category $record): void
    {
        $this->guardSlugIsUnique($entity);

        $record->id = $entity->getId()->getId();
        $record->name = $entity->getName();
        $record->slug = $entity->getSlug();
        $record->parent_id = $entity->getParent()?->getId();
        $record->created_at = $entity->getCreatedAt()->getTimestamp();
        $record->updated_at = $entity->getUpdatedAt()?->getTimestamp();
        $record->save();
        $this->hydrator->hydrate($entity->getId(), ['id' => $record->id]);
        $this->persistProducts($entity, $record);
    }

    private function guardSlugIsUnique(Entity\Category $entity): void
    {
        $slug = $entity->getSlug();
        $notUnique = Eloquent\Category::query()
            ->where('slug', $slug)
            ->where('id', '!=', $entity->getId()->getId())
            ->exists();

        if ($notUnique) {
            throw new DuplicateKeyException('Category with same slug already exists');
        }
    }

    private function persistProducts(Entity\Category $entity, Eloquent\Category $record): void
    {
        $record->productsRef()->delete();
        foreach ($entity->getProducts() as $product) {
            $record->productsRef()->create([
                'product_id' => $product
            ]);
        }
    }

    public function update(Entity\Category $entity): void
    {
        $id = $entity->getId()->getId();
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Category does not exists');
        }

        if (!$record = Eloquent\Category::find($id)) {
            throw new NotFoundException('Category does not exists');
        }

        $this->persist($entity, $record);
    }

    public function delete(Entity\Category $entity): void
    {
        $id = $entity->getId()->getId();
        if (empty($id) || !$this->identityMap->has($id)) {
            throw new NotFoundException('Category does not exists');
        }

        if (!$record = Eloquent\Category::find($id)) {
            throw new NotFoundException('Category does not exists');
        }

        $this->identityMap->remove($id);
        $record->delete();
    }

    public function get(Entity\CategoryId $id): Entity\Category
    {
        if (empty($id->getId())) {
            throw new NotFoundException('Category does not exists');
        }

        if ($this->identityMap->has($id->getId())) {
            return $this->identityMap->get($id->getId());
        }

        if (!$record = Eloquent\Category::find($id->getId())) {
            throw new NotFoundException('Category does not exists');
        }

        $entity = $this->hydrate($record);
        $this->identityMap->add($id->getId(), $entity);
        return $entity;
    }

    private function hydrate(Eloquent\Category $record): Entity\Category
    {
        return $this->hydrator->hydrate(Entity\Category::class, [
            'id' => new Entity\CategoryId($record->id),
            'name' => $record->name,
            'slug' => $record->slug,
            'parent' => $record->parent_id
                ? new Entity\CategoryId($record->parent_id)
                : null,
            'products' => array_column($record->productsRef->all(), 'product_id'),
            'createdAt' => new \DateTimeImmutable($record->created_at),
            'updatedAt' => $record->updated_at
                ? new \DateTimeImmutable($record->updated_at)
                : null,
        ]);
    }
}
