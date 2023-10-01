<?php

namespace Project\Modules\Client\Repository;

use Project\Modules\Client\Entity;

interface ClientsRepositoryInterface
{
    public function add(Entity\Client $client): void;

    public function update(Entity\Client $client): void;

    public function delete(Entity\Client $client): void;

    public function get(Entity\ClientId|Entity\ClientHash $id): Entity\Client;
}