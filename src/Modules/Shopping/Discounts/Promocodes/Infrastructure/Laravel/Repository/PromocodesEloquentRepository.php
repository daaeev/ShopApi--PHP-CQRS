<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Models as Eloquent;
use Project\Modules\Shopping\Discounts\Promocodes\Infrastructure\Laravel\Utils\PromocodeEloquentToEntityConverter;

class PromocodesEloquentRepository implements PromocodesRepositoryInterface
{
    public function __construct(
        private Hydrator $hydrator,
		private IdentityMap $identityMap,
        private PromocodeEloquentToEntityConverter $converter
    ) {}

    public function add(Entity\Promocode $promocode): void
    {
        $id = $promocode->getId()->getId();
		if (!empty($id) && $this->identityMap->has($id)) {
			throw new DuplicateKeyException('Promocode with same id already exists');
		}

        if (Eloquent\Promocode::find($id)) {
            throw new DuplicateKeyException('Promocode with same id already exists');
        }

        $this->persist($promocode, new Eloquent\Promocode);
		$this->identityMap->add($promocode->getId()->getId(), $promocode);
		$this->identityMap->add($promocode->getCode(), $promocode);
    }

    private function persist(Entity\Promocode $entity, Eloquent\Promocode $record): void
    {
        $this->guardCodeUnique($entity);

        $record->id = $entity->getId()->getId();
        $record->name = $entity->getName();
        $record->code = $entity->getCode();
        $record->discount_percent = $entity->getDiscountPercent();
        $record->active = $entity->getActive();
        $record->start_date = $entity->getStartDate()->format(\DateTimeInterface::RFC3339);
        $record->end_date = $entity->getEndDate()?->format(\DateTimeInterface::RFC3339);
        $record->created_at = $entity->getCreatedAt()->format(\DateTimeInterface::RFC3339);
        $record->updated_at = $entity->getUpdatedAt()?->format(\DateTimeInterface::RFC3339);
        $record->save();

        $this->hydrator->hydrate($entity->getId(), ['id' => $record->id]);
    }

    private function guardCodeUnique(Entity\Promocode $promocode): void
    {
		if ($this->identityMap->has($promocode->getCode())) {
			$samePromocode = $this->identityMap->get($promocode->getCode());
			if (!$promocode->getId()->equalsTo($samePromocode->getId())) {
				throw new DuplicateKeyException('Promocode must be unique');
			}

			return;
		}

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
		if (empty($id) || !$this->identityMap->has($id)) {
			throw new NotFoundException('Promocode does not exists');
		}

		if (!$record = Eloquent\Promocode::find($id)) {
            throw new NotFoundException('Promocode does not exists');
        }

        $this->persist($promocode, $record);
    }

    public function delete(Entity\Promocode $promocode): void
    {
        $id = $promocode->getId()->getId();
		if (empty($id) || !$this->identityMap->has($id)) {
			throw new NotFoundException('Promocode does not exists');
		}

        if (!$record = Eloquent\Promocode::find($id)) {
            throw new NotFoundException('Promocode does not exists');
        }

        $record->delete();
		$this->identityMap->remove($id);
		$this->identityMap->remove($promocode->getCode());
    }

    public function get(Entity\PromocodeId $id): Entity\Promocode
    {
		if (empty($id->getId())) {
			throw new NotFoundException('Promocode does not exists');
		}

		if ($this->identityMap->has($id->getId())) {
			return $this->identityMap->get($id->getId());
		}

        if (!$record = Eloquent\Promocode::find($id->getId())) {
            throw new NotFoundException('Promocode does not exists');
        }

        $promocode = $this->converter->convert($record);
		$this->identityMap->add($promocode->getId()->getId(), $promocode);
		$this->identityMap->add($promocode->getCode(), $promocode);
		return $promocode;
    }

    public function getByCode(string $code): Entity\Promocode
    {
		if ($this->identityMap->has($code)) {
			return $this->identityMap->get($code);
		}

        $record = Eloquent\Promocode::query()->where('code', $code)->first();
        if (!$record) {
            throw new NotFoundException('Promocode does not exists');
        }

		$promocode = $this->converter->convert($record);
		$this->identityMap->add($promocode->getId()->getId(), $promocode);
		$this->identityMap->add($promocode->getCode(), $promocode);
		return $promocode;
    }
}