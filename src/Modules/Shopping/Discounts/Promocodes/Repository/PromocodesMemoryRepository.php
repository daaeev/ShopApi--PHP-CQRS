<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Discounts\Promocodes\Entity;

class PromocodesMemoryRepository implements PromocodesRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator
    ) {}

    public function add(Entity\Promocode $promocode): void
    {
        $this->guardCodeUnique($promocode);

        if (null === $promocode->getId()->getId()) {
            $this->hydrator->hydrate($promocode->getId(), ['id' => ++$this->increment]);
        }

        if (isset($this->items[$promocode->getId()->getId()])) {
            throw new DuplicateKeyException('Promocode with same id already exists');
        }

        $this->items[$promocode->getId()->getId()] = clone $promocode;
    }

    private function guardCodeUnique(Entity\Promocode $promocode): void
    {
        foreach ($this->items as $item) {
            if ($promocode->getId()->equalsTo($item->getId())) {
                continue;
            }

            if ($promocode->getCode() === $item->getCode()) {
                throw new DuplicateKeyException('Promo-code must be unique');
            }
        }
    }

    public function update(Entity\Promocode $promocode): void
    {
        $this->guardCodeUnique($promocode);

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

        unset($this->items[$promocode->getId()->getId()]);
    }

    public function get(Entity\PromocodeId $id): Entity\Promocode
    {
        if (empty($this->items[$id->getId()])) {
            throw new NotFoundException('Promocode does not exists');
        }

        return clone $this->items[$id->getId()];
    }

    public function getByCode(string $code): Entity\Promocode
    {
        $promocode = null;
        foreach ($this->items as $item) {
            if ($item->getCode() === $code) {
                $promocode = $item;
            }
        }

        if ($promocode === null) {
            throw new \DomainException('Promo-code must be unique');
        }

        return clone $promocode;
    }
}