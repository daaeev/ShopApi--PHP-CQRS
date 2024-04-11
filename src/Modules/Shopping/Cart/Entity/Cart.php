<?php

namespace Project\Modules\Shopping\Cart\Entity;

use Webmozart\Assert\Assert;
use Project\Common\Client\Client;
use Project\Common\Entity\Aggregate;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;
use Project\Modules\Shopping\Api\Events\Cart\CartDeactivated;
use Project\Modules\Shopping\Api\Events\Cart\CartInstantiated;
use Project\Modules\Shopping\Api\Events\Cart\CartCurrencyChanged;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeAddedToCart;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\Promocode;
use Project\Modules\Shopping\Api\Events\Cart\PromocodeRemovedFromCart;

class Cart extends Aggregate
{
    private CartId $id;
    private Client $client;
    private array $items;
    private Currency $currentCurrency;
    private bool $active = true;
    private ?Promocode $promocode = null;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        CartId $id,
        Client $client,
        array $items = []
    ) {
        $this->id = $id;
        $this->client = $client;
        $this->items = $items;
        $this->currentCurrency = Currency::default();
        $this->createdAt = new \DateTimeImmutable;
        $this->guardValidItems();
        $this->addEvent(new CartInstantiated($this));
    }

    private function guardValidItems()
    {
        Assert::allIsInstanceOf($this->items, CartItem::class);
    }

	public function __clone(): void
	{
		$this->id = clone $this->id;
		$this->client = clone $this->client;
		$this->promocode = $this->promocode ? clone $this->promocode : null;
		$this->createdAt = clone $this->createdAt;
		$this->updatedAt = $this->updatedAt ? clone $this->updatedAt : null;
		foreach ($this->items as $index => $cartItem) {
			$this->items[$index] = clone $cartItem;
		}
	}

	public static function instantiate(Client $client): self
    {
        return new self(
            CartId::next(),
            $client
        );
    }

    public function addItem(CartItem $newItem): void
    {
        if ($sameItem = $this->getSameItem($newItem)) {
            $this->replaceItem($sameItem, $newItem);
        } else {
            $this->items[] = $newItem;
        }

        $this->updated();
    }

    private function getSameItem(CartItem $item): ?CartItem
    {
        foreach ($this->items as $currentItem) {
            if ($item->getId()->equalsTo($currentItem->getId()) || $currentItem->equalsTo($item)) {
                return $currentItem;
            }
        }

        return null;
    }

    private function replaceItem(CartItem $old, CartItem $new): void
    {
        foreach ($this->items as $index => $currentItem) {
            if ($old->getId()->equalsTo($currentItem->getId()) || $currentItem->equalsTo($old)) {
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

    private function updated(): void
    {
        $this->addEvent(new CartUpdated($this));
        $this->updatedAt = new \DateTimeImmutable;
    }

	public function setItems(array $cartItems): void
	{
        Assert::allIsInstanceOf($cartItems, CartItem::class);
		$this->items = $cartItems;
        $this->updated();
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
        $this->updated();
    }

    public function changeCurrency(Currency $currency): void
    {
        if ($currency === $this->currentCurrency) {
            return;
        }

        if (!$currency->isActive()) {
            throw new \DomainException('Cant update cart currency to inactive currency ');
        }

        $this->currentCurrency = $currency;
        $this->addEvent(new CartCurrencyChanged($this));
        $this->updated();
    }

    public function usePromocode(Promocode $promocode): void
    {
        if (null !== $this->promocode) {
            throw new \DomainException('Other promocode already used');
        }

        if (empty($promocode->getId()->getId())) {
            throw new \DomainException('Promocode id cant be empty');
        }

        if (!$promocode->isActive()) {
            throw new \DomainException('Cant use not active promo-code');
        }

        $this->promocode = $promocode;
        $this->addEvent(new PromocodeAddedToCart($this));
        $this->updated();
    }

    public function removePromocode(): void
    {
        if (null === $this->promocode) {
            throw new \DomainException('Cart does not have promo-code');
        }

        $this->promocode = null;
        $this->addEvent(new PromocodeRemovedFromCart($this));
        $this->updated();
    }

    public function getTotalPrice(): float
    {
        $totalPrice = array_reduce($this->items, function ($totalPrice, $item) {
            return $totalPrice + ($item->getPrice() * $item->getQuantity());
        }, 0);

        if (null !== $this->promocode) {
            $discountPrice = ($totalPrice / 100) * $this->promocode->getDiscountPercent();
            $totalPrice -= $discountPrice;
        }

        return $totalPrice;
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

    /**
     * @return CartItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getCurrency(): Currency
    {
        return $this->currentCurrency;
    }

    public function getPromocode(): ?Promocode
    {
        return $this->promocode;
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