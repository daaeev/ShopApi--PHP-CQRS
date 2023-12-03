<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Repository;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Modules\Shopping\Discounts\Promotions\Entity;

class PromotionsMemoryRepository implements PromotionsRepositoryInterface
{
    private array $items = [];
    private int $increment = 0;

    public function __construct(
        private Hydrator $hydrator
    ) {}

    public function add(Entity\Promotion $promotion): void
    {
        if (null === $promotion->getId()->getId()) {
            $this->hydrator->hydrate($promotion->getId(), ['id' => ++$this->increment]);
        }

        if (isset($this->items[$promotion->getId()->getId()])) {
            throw new DuplicateKeyException('Promotion with same id already exists');
        }

        $this->items[$promotion->getId()->getId()] = clone $promotion;
    }

    public function update(Entity\Promotion $promotion): void
    {
        if (!isset($this->items[$promotion->getId()->getId()])) {
            throw new NotFoundException('Promotion does not exists');
        }

        $this->items[$promotion->getId()->getId()] = clone $promotion;
    }

    public function delete(Entity\Promotion $promotion): void
    {
        if (!isset($this->items[$promotion->getId()->getId()])) {
            throw new NotFoundException('Promotion does not exists');
        }

        unset($this->items[$promotion->getId()->getId()]);
    }

    public function get(Entity\PromotionId $id): Entity\Promotion
    {
        if (!isset($this->items[$id->getId()])) {
            throw new NotFoundException('Promotion does not exists');
        }

        return clone $this->items[$id->getId()];
    }
}