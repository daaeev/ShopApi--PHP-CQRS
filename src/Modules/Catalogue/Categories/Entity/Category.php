<?php

namespace Project\Modules\Catalogue\Categories\Entity;

use Project\Common\Entity\Aggregate;
use Webmozart\Assert\Assert;
use Project\Modules\Catalogue\Api\Events\Category as CategoryEvents;

class Category extends Aggregate
{
    private CategoryId $id;
    private string $name;
    private string $slug;
    private array $products = [];
    private ?CategoryId $parent = null;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        CategoryId $id,
        string $name,
        string $slug,
    ) {
        Assert::notEmpty($name && $slug);

        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
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
            throw new \DomainException('Product already attached');
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

    public function attachParent(CategoryId $parentId): void
    {
        if (!empty($this->parent) && $this->parent->equalsTo($parentId)) {
            throw new \DomainException('Same parent category already attached');
        }

        if ($parentId->equalsTo($this->id)) {
            throw new \DomainException('Cant attach same category as parent');
        }

        $this->parent = $parentId;
        $this->updated();
    }

    public function detachParent(): void
    {
        if (empty($this->parent)) {
            throw new \DomainException('Category does not have parent category');
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