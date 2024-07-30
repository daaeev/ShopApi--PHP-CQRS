<?php

namespace Project\Modules\Shopping\Order\Entity;

class Manager
{
    public function __construct(
        private ManagerId $managerId,
        private ?string $name,
    ) {}

    public function getId(): ManagerId
    {
        return $this->managerId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}