<?php

namespace Project\Modules\Cart\Entity;

use Project\Common\Events;
use Webmozart\Assert\Assert;
use Project\Common\Environment\Client\Client;

class Cart implements Events\EventRoot
{
    use Events\EventTrait;

    public function __construct(
        private CartId $id,
        private Client $client,
        private array $items = []
    ) {
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
            return;
        }

        if ($this->getItem($item->getProduct())->equalsTo($item)) {
            return;
        }

        $this->removeItem($item->getProduct());
        $this->items[] = $item;
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
                return;
            }
        }

        throw new \DomainException('Cart item not found');
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
}