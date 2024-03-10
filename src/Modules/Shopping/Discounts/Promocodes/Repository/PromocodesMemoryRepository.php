<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Repository;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;

class PromocodesMemoryRepository implements PromocodesRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator,
		private IdentityMap $identityMap,
    ) {}

    public function add(Entity\Promocode $promocode): void
    {
        $this->guardCodeUnique($promocode);

        if (null === $promocode->getId()->getId()) {
            $this->hydrator->hydrate($promocode->getId(), ['id' => ++$this->increment]);
        }

        if ($this->identityMap->has($promocode->getId()->getId())) {
            throw new DuplicateKeyException('Promocode with same id already exists');
        }

        $this->items[$promocode->getId()->getId()] = clone $promocode;
		$this->identityMap->add($promocode->getId()->getId(), $promocode);
		$this->identityMap->add($promocode->getCode(), $promocode);
    }

    private function guardCodeUnique(Entity\Promocode $promocode): void
    {
		if (!$this->identityMap->has($promocode->getCode())) {
			return;
		}

		$samePromocode = $this->identityMap->get($promocode->getCode());
		if (!$promocode->getId()->equalsTo($samePromocode->getId())) {
			throw new DuplicateKeyException('Promocode must be unique');
		}
    }

    public function update(Entity\Promocode $promocode): void
    {
        if (empty($this->items[$promocode->getId()->getId()])) {
            throw new NotFoundException('Promocode does not exists');
        }

        $this->items[$promocode->getId()->getId()] = clone $promocode;
    }

    public function delete(Entity\Promocode $promocode): void
    {
        if (empty($this->items[$promocode->getId()->getId()])) {
            throw new NotFoundException('Promocode does not exists');
        }

		$this->identityMap->remove($promocode->getId()->getId());
		$this->identityMap->remove($promocode->getCode());
		unset($this->items[$promocode->getId()->getId()]);
    }

    public function get(Entity\PromocodeId $id): Entity\Promocode
    {
        if (empty($id->getId())) {
            throw new NotFoundException('Promocode does not exists');
        }

		if (!$this->identityMap->has($id->getId())) {
			throw new NotFoundException('Promocode does not exists');
		}

		return $this->identityMap->get($id->getId());
    }

    public function getByCode(string $code): Entity\Promocode
    {
		if (!$this->identityMap->has($code)) {
			throw new NotFoundException('Promocode does not exists');
		}

		return $this->identityMap->get($code);
	}
}