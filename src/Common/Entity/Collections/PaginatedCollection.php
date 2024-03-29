<?php

namespace Project\Common\Entity\Collections;

class PaginatedCollection extends Collection
{
    private Pagination $pagination;

    public function __construct(array $entities, Pagination $pagination)
    {
        $this->pagination = $pagination;
        parent::__construct($entities);
    }

    public function toArray(): array
    {
        return [
            'items' => parent::toArray(),
            'pagination' => $this->pagination->toArray()
        ];
    }
}