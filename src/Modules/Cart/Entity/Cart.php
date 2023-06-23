<?php

namespace Project\Modules\Cart\Entity;

use Project\Common\Events;
use Webmozart\Assert\Assert;
use Project\Common\Environment\Client\Client;

class Cart implements Events\EventRoot
{
    use Events\EventTrait;

    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        private CartId $id,
        private Client $client,
        private array $items = []
    ) {
        $this->createdAt = new \DateTimeImmutable;
        $this->guardValidItems();
    }

    private function guardValidItems()
    {
        Assert::allIsInstanceOf($this->items, CartItem::class);
    }

    public function addItem(CartItem $item): void
    {
        if (!$this->itemExists($item->getProduct())) {
            $this->items[] = $item;
            $this->updated();
            return;
        }

        if ($this->getItem($item->getProduct())->equalsTo($item)) {
            return;
        }

        $this->removeItem($item->getProduct());
        $this->items[] = $item;
        $this->updated();
    }

    private function itemExists(int|string $product): bool
    {
        foreach ($this->items as $currentItem) {
            if ($currentItem->getProduct() === $product) {
                return true;
            }
        }

        return false;
    }

    public function getItem(int|string $product): CartItem
    {
        foreach ($this->items as $currentItem) {
            if ($currentItem->getProduct() === $product) {
                return $currentItem;
            }
        }

        throw new \DomainException('Cart item not found');
    }

    public function removeItem(int|string $product): void
    {
        foreach ($this->items as $index => $currentItem) {
            if ($currentItem->getProduct() === $product) {
                unset($this->items[$index]);
                $this->updated();
                return;
            }
        }

        throw new \DomainException('Cart item not found');
    }

    private function updated(): void
    {
        $this->updatedAt = new \DateTimeImmutable;
    }

    public static function instantiate(Client $client): self
    {
        return new self(
            CartId::next(),
            $client
        );
    }

    public function getId(): CartId
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getItems(): array
    {
        return $this->items;
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