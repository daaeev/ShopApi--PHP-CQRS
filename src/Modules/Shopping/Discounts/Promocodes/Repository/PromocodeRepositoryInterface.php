<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Repository;

use Project\Modules\Shopping\Discounts\Promocodes\Entity;

interface PromocodeRepositoryInterface
{
    public function add(Entity\Promocode $promocode): void;

    public function update(Entity\Promocode $promocode): void;

    public function delete(Entity\Promocode $promocode): void;

    public function get(Entity\PromocodeId $id): Entity\Promocode;

    public function getByCode(string $code): Entity\Promocode;
}