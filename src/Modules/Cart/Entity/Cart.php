<?php

namespace Project\Modules\Cart\Entity;

use Project\Common\Events;
use Webmozart\Assert\Assert;
use Project\Common\Currency;
use Project\Common\Environment\Client\Client;
use Project\Modules\Cart\Api\Events\CartItemAdded;
use Project\Modules\Cart\Api\Events\CartItemRemoved;
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

    public function addItem(CartItem $item): void
    {
        if (!$this->itemExists($item->getProduct())) {
            $this->items[] = $item;
            $this->updated();
            $this->addEvent(new CartItemAdded($this));
            return;
        }

        if ($this->getItem($item->getProduct())->equalsTo($item)) {
            return;
        }

        $this->removeItem($item->getProduct());
        $this->items[] = $item;
        $this->addEvent(new CartItemAdded($this));
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
                $this->addEvent(new CartItemRemoved($this));
                return;
            }
        }

        throw new \DomainException('Cart item not found');
    }

    private function updated(): void
    {
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function deactivate(): void
    {
        if (false === $this->active) {
            throw new \DomainException('Cart already deactivated');
        }

        $this->active = false;
        $this->addEvent(new CartDeactivated($this));
        $this->updated();
    }

    public function changeCurrency(Currency $currency): void
    {
        if ($currency === $this->currentCurrency) {
            return;
        }

        $this->currentCurrency = $currency;
        $this->updated();
        $this->addEvent(new CartCurrencyChanged($this));
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