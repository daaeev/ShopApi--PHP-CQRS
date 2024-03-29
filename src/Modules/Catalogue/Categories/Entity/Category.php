<?php

namespace Project\Modules\Catalogue\Categories\Entity;

use Project\Common\Events;
use Webmozart\Assert\Assert;
use Project\Modules\Catalogue\Api\Events\Category as CategoryEvents;

class Category implements Events\EventRoot
{
    use Events\EventTrait;

    private array $products = [];
    private ?CategoryId $parent = null;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        private CategoryId $id,
        private string $name,
        private string $slug,
    ) {
        Assert::notEmpty($name && $slug);
        $this->createdAt = new \DateTimeImmutable;
        $this->addEvent(new CategoryEvents\CategoryCreated($this));
    }

    public function updateName(string $name): void
    {
        if ($name === $this->name) {
            return;
        }

        Assert::notEmpty($name);
        $this->name = $name;
        $this->updated();
    }

    private function updated(): void
    {
        $this->updatedAt = new \DateTimeImmutable;
        $this->addEvent(new CategoryEvents\CategoryUpdated($this));
    }

    public function updateSlug(string $slug): void
    {
        if ($slug === $this->slug) {
            return;
        }

        Assert::notEmpty($slug);
        $this->slug = $slug;
        $this->updated();
    }

    public function attachProduct(int $product): void
    {
        if (in_array($product, $this->products)) {
            return;
        }

        $this->products[] = $product;
        $this->updated();
    }

    public function detachProducts(): void
    {
        if (empty($this->products)) {
            return;
        }

        $this->products = [];
        $this->updated();
    }

    public function attachParent(CategoryId $parent): void
    {
        if (!empty($this->parent) && $parent->equalsTo($this->parent)) {
            return;
        }

        if ($parent->equalsTo($this->id)) {
            throw new \DomainException('Cant attach same category as parent');
        }

        $this->parent = $parent;
        $this->updated();
    }

    public function detachParent(): void
    {
        if (empty($this->parent)) {
            return;
        }

        $this->parent = null;
        $this->updated();
    }

    public function delete(): void
    {
        $this->addEvent(new CategoryEvents\CategoryDeleted($this));
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getParent(): ?CategoryId
    {
        return $this->parent;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}