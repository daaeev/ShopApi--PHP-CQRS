<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\PromocodeEloquent2EntityConverter;

class PromocodesEloquentRepository implements PromocodesRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
        private PromocodeEloquent2EntityConverter $converter
    ) {}

    public function add(Entity\Promocode $promocode): void
    {
        $id = $promocode->getId()->getId();

        if (Eloquent\Promocode::find($id)) {
            throw new DuplicateKeyException('Promocode with same id already exists');
        }

        $this->persist($promocode, new Eloquent\Promocode);
    }

    private function persist(Entity\Promocode $entity, Eloquent\Promocode $record): void
    {
        $this->guardCodeUnique($entity);

        if (!$record->exists) {
            $record->id = $entity->getId()->getId();
            $record->created_at = $entity->getCreatedAt()->format(\DateTimeInterface::RFC3339);
        }

        $record->name = $entity->getName();
        $record->code = $entity->getCode();
        $record->discount_percent = $entity->getDiscountPercent();
        $record->active = $entity->getActive();
        $record->start_date = $entity->getStartDate()->format(\DateTimeInterface::RFC3339);
        $record->end_date = $entity->getEndDate()?->format(\DateTimeInterface::RFC3339);
        $record->updated_at = $entity->getUpdatedAt()?->format(\DateTimeInterface::RFC3339);
        $record->save();
        $this->hydrator->hydrate($entity->getId(), ['id' => $record->id]);
    }

    private function guardCodeUnique(Entity\Promocode $promocode): void
    {
        $notUnique = Eloquent\Promocode::query()
            ->where('code', $promocode->getCode())
            ->where('id', '!=', $promocode->getId()->getId())
            ->exists();

        if ($notUnique) {
            throw new DuplicateKeyException('Promocode must be unique');
        }
    }

    public function update(Entity\Promocode $promocode): void
    {
        $id = $promocode->getId()->getId();

        if (!$record = Eloquent\Promocode::find($id)) {
            throw new NotFoundException('Promocode does not exists');
        }

        $this->persist($promocode, $record);
    }

    public function delete(Entity\Promocode $promocode): void
    {
        $id = $promocode->getId()->getId();

        if (!$record = Eloquent\Promocode::find($id)) {
            throw new NotFoundException('Promocode does not exists');
        }

        $record->delete();
    }

    public function get(Entity\PromocodeId $id): Entity\Promocode
    {
        if (!$record = Eloquent\Promocode::find($id->getId())) {
            throw new NotFoundException('Promocode does not exists');
        }

        return $this->converter->convert($record);
    }

    public function getByCode(string $code): Entity\Promocode
    {
        $record = Eloquent\Promocode::query()
            ->where('code', $code)
            ->first();

        if (!$record) {
            throw new NotFoundException('Promocode does not exists');
        }

        return $this->converter->convert($record);
    }
}