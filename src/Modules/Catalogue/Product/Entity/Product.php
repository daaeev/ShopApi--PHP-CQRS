<?php

namespace Project\Modules\Catalogue\Product\Entity;

use Project\Common\Entity\Aggregate;
use Webmozart\Assert\Assert;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Catalogue\Api\Events\Product as ProductEvents;

class Product extends Aggregate
{
    private ProductId $id;
    private string $name;
    private string $code;
    private bool $active = true;
    private Availability $availability = Availability::IN_STOCK;
    private array $colors = [];
    private array $sizes = [];
    private array $prices;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        ProductId $id,
        string $name,
        string $code,
        array $prices
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->prices = $prices;
        $this->createdAt = new \DateTimeImmutable;
        $this->guardCorrectConstructData();
        $this->keepPricesUnique();
        $this->addEvent(new ProductEvents\ProductCreated($this));
    }

    private function guardCorrectConstructData(): void
    {
        $this->guardNotEmptyName();
        $this->guardNotEmptyCode();
        Assert::allIsInstanceOf(
            $this->prices,
            Price\Price::class,
            'Product prices must be instances if ' . Price\Price::class
        );
        $this->guardContainsAllCurrencyPricesIfActive();
    }

    private function guardNotEmptyName(): void
    {
        Assert::notEmpty($this->name, 'Product name can not be empty');
    }

    private function guardNotEmptyCode(): void
    {
        Assert::notEmpty($this->code, 'Product code can not be empty');
    }

    private function guardContainsAllCurrencyPricesIfActive(): void
    {
        if (!$this->active) {
            return;
        }

        foreach (Currency::active() as $currency) {
            if (!$this->priceByCurrencyExists($currency)) {
                throw new \DomainException('Product does not contain price for ' . $currency->value . ' currency');
            }
        }
    }

    private function priceByCurrencyExists(Currency $currency): bool
    {
        foreach ($this->prices as $price) {
            if ($price->getCurrency() === $currency) {
                return true;
            }
        }

        return false;
    }

    private function keepPricesUnique(): void
    {
        $prices = [];
        foreach ($this->prices as $price) {
            $prices[$price->getCurrency()->value] = $price;
        }

        $this->prices = $prices;
    }

    public function delete(): void
    {
        if ($this->active) {
            throw new \DomainException('Cant delete active product. You need to disable it before deleting');
        }

        $this->addEvent(new ProductEvents\ProductDeleted($this));
    }

    public function setName(string $name): void
    {
        if ($name === $this->name) {
            return;
        }

        $this->name = $name;
        $this->guardNotEmptyName();
        $this->updated();
    }

    public function setCode(string $code): void
    {
        if ($code === $this->code) {
            return;
        }

        $this->code = $code;
        $this->guardNotEmptyCode();
        $this->addEvent(new ProductEvents\ProductCodeChanged($this));
        $this->updated();
    }

    private function updated(): void
    {
        $this->addEvent(new ProductEvents\ProductUpdated($this));
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function activate()
    {
        if (true === $this->active) {
            return;
        }

        $this->active = true;
        $this->guardContainsAllCurrencyPricesIfActive();
        $this->addEvent(new ProductEvents\ProductActivityChanged($this));
        $this->updated();
    }

    public function deactivate()
    {
        if (false === $this->active) {
            return;
        }

        $this->active = false;
        $this->addEvent(new ProductEvents\ProductActivityChanged($this));
        $this->updated();
    }

    public function setAvailability(Availability $availability): void
    {
        if ($availability === $this->availability) {
            return;
        }

        $this->availability = $availability;
        $this->addEvent(new ProductEvents\ProductAvailabilityChanged($this));
        $this->updated();
    }

    public function setPrices(array $prices): void
    {
        Assert::allIsInstanceOf(
            $prices,
            Price\Price::class,
            'Product prices must be instances if ' . Price\Price::class
        );

        if ($this->samePrices($prices)) {
            return;
        }

        $this->prices = $prices;
        $this->guardContainsAllCurrencyPricesIfActive();
        $this->keepPricesUnique();
        $this->addEvent(new ProductEvents\ProductPricesChanged($this));
        $this->updated();
    }

    private function samePrices(array $prices): bool
    {
        if (count($this->prices) !== count($prices)) {
            return false;
        }

        foreach ($prices as $price) {
            if (
                empty($this->prices[$price->getCurrency()->value])
                || !$price->equalsTo($this->prices[$price->getCurrency()->value])
            ) {
                return false;
            }
        }

        return true;
    }

    public function setColors(array $colors): void
    {
        if ($this->sameColors($colors)) {
            return;
        }

        $this->colors = $colors;
        $this->keepColorsUnique();
        $this->updated();
    }

    private function sameColors(array $colors): bool
    {
        if (count($this->colors) !== count($colors)) {
            return false;
        }

        foreach ($colors as $color) {
            if (!in_array($color, $this->colors)) {
                return false;
            }
        }

        return true;
    }

    private function keepColorsUnique(): void
    {
        $this->colors = array_unique($this->colors);
    }

    public function setSizes(array $sizes): void
    {
        if ($this->sameSizes($sizes)) {
            return;
        }

        $this->sizes = $sizes;
        $this->keepSizesUnique();
        $this->updated();
    }

    private function sameSizes(array $sizes): bool
    {
        if (count($this->sizes) !== count($sizes)) {
            return false;
        }

        foreach ($sizes as $size) {
            if (!in_array($size, $this->sizes)) {
                return false;
            }
        }

        return true;
    }

    private function keepSizesUnique(): void
    {
        $this->sizes = array_unique($this->sizes);
    }

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getAvailability(): Availability
    {
        return $this->availability;
    }

    public function getColors(): array
    {
        return $this->colors;
    }

    public function getSizes(): array
    {
        return $this->sizes;
    }

    /**
     * @return Price\Price[]
     */
    public function getPrices(): array
    {
        return $this->prices;
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
