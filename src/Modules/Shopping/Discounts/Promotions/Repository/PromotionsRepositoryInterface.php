<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Repository;

use Project\Modules\Shopping\Discounts\Promotions\Entity;

interface PromotionsRepositoryInterface
{
    public function add(Entity\Promotion $promotion): void;

    public function update(Entity\Promotion $promotion): void;

    public function delete(Entity\Promotion $promotion): void;

    public function get(Entity\PromotionId $id): Entity\Promotion;

    public function getActivePromotions(): array;
}