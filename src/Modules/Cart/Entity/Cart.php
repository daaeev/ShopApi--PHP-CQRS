<?php

namespace Project\Modules\Cart\Entity;

use Project\Common\Events;
use Webmozart\Assert\Assert;
use Project\Common\Product\Currency;
use Project\Common\Environment\Client\Client;
use Project\Modules\Cart\Api\Events\CartUpdated;
use Project\Modules\Cart\Api\Events\CartDeactivated;
use Project\Modules\Cart\Api\Events\CartInstantiated;
use Project\Modules\Cart\Api\Events\CartCurrencyChanged;

class Cart implements Events\EventRoot
{
    use Events\EventTrait;

    private Currency $currentCurrency;
    private bool $active = true;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        private CartId $id,
        private Client $client,
        private array $items = []
    ) {
        $this->currentCurrency = Currency::default();
        $this->createdAt = new \DateTimeImmutable;
        $this->guardValidItems();
        $this->addEvent(new CartInstantiated($this));
    }

    private function guardValidItems()
    {
        Assert::allIsInstanceOf($this->items, CartItem::class);
    }

    public function addItem(CartItem $newItem): void
    {
        if (!$this->sameItemExists($newItem)) {
            $this->items[] = $newItem;
            $this->updated();
            return;
        }

        $sameItem = $this->getSameItem($newItem);

        if ($sameItem->getQuantity() === $newItem->getQuantity()) {
            return;
        }

        $this->replaceItem($sameItem, $newItem);
        $this->updated();
    }

    private function sameItemExists(CartItem $item): bool
    {
        foreach ($this->items as $currentItem) {
            if ($currentItem->equalsTo($item)) {
                return true;
            }
        }

        return false;
    }

    private function getSameItem(CartItem $item): CartItem
    {
        foreach ($this->items as $currentItem) {
            if ($currentItem->equalsTo($item)) {
                return $currentItem;
            }
        }

        throw new \DomainException('Cart item not found');
    }

    private function replaceItem(CartItem $old, CartItem $new): void
    {
        foreach ($this->items as $index => $currentItem) {
            if ($currentItem->equalsTo($old)) {
                $this->items[$index] = $new;
            }
        }
    }

    public function getItem(CartItemId $itemId): CartItem
    {
        foreach ($this->items as $currentItem) {
            if ($currentItem->getId()->equalsTo($itemId)) {
                return $currentItem;
            }
        }

        throw new \DomainException('Cart item not found');
    }

    public function removeItem(CartItemId $itemId): void
    {
        foreach ($this->items as $index => $currentItem) {
            if ($currentItem->getId()->equalsTo($itemId)) {
                unset($this->items[$index]);
                $this->updated();
                return;
            }
        }

        throw new \DomainException('Cart item not found');
    }

    private function updated(bool $addEvent = true): void
    {
        if ($addEvent) {
            $this->addEvent(new CartUpdated($this));
        }

        $this->updatedAt = new \DateTimeImmutable;
    }

    public function deactivate(): void
    {
        if (false === $this->active) {
            throw new \DomainException('Cart already deactivated');
        }

        if (empty($this->items)) {
            throw new \DomainException('Cant deactivate empty cart');
        }

        $this->active = false;
        $this->addEvent(new CartDeactivated($this));
        $this->updated(false);
    }

    public function changeCurrency(Currency $currency): void
    {
        if ($currency === $this->currentCurrency) {
            return;
        }

        $this->currentCurrency = $currency;
        $this->addEvent(new CartCurrencyChanged($this));
        $this->updated(false);
    }

    public static function instantiate(Client $client): self
    {
        return new self(
            CartId::next(),
            $client
        );
    }

    public function getTotalPrice(): float
    {
        return array_reduce($this->items, function ($totalPrice, $item) {
            return $totalPrice + ($item->getPrice() * $item->getQuantity());
        }, 0);
    }

    public function getId(): CartId
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCurrency(): Currency
    {
        return $this->currentCurrency;
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