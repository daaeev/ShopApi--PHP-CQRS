<?php

namespace Project\Common\Entity\Collections;

use Project\Common\Utils\Arrayable;

class Pagination implements Arrayable
{
    public function __construct(
        private int $page,
        private int $limit,
        private int $total,
    ) {}

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'total' => $this->total,
        ];
    }
}