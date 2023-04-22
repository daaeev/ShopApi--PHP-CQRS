<?php

namespace Project\Modules\Product\Entity;

use DomainException;
use Webmozart\Assert\Assert;
use Project\Common\Events;

class Product implements Events\EventRoot
{
    use Events\EventTrait;

    private ProductId $id;
    private string $name;
    private string $code;
    private bool $active;
    private Availability $availability;
    private array $colors;
    private array $sizes;
    private array $prices;

    public function __construct(

    ) {
        $this->guardCorrectData();
        $this->normalizeColors();
        $this->normalizePrices();
        $this->normalizeSizes();
    }

    private function guardCorrectData()
    {
        Assert::notEmpty($this->name, 'Product name can not be empty');
        Assert::notEmpty($this->code, 'Product code can not be empty');
        Assert::allIsInstanceOf(
            $this->colors,
            Color\Color::class,
            'Product colors must be instances if ' . Color\Color::class
        );
        Assert::allIsInstanceOf(
            $this->colors,
            Price\Price::class,
            'Product prices must be instances if ' . Price\Price::class
        );
        Assert::allIsInstanceOf(
            $this->sizes,
            Size\Size::class,
            'Product sizes must be instances if ' . Size\Size::class
        );
        $this->guardContainsAllActiveCurrenciesPricesIfActive();
    }

    private function guardContainsAllActiveCurrenciesPricesIfActive()
    {
        if (!$this->active) {
            return;
        }

        if (count(Price\Currency::active()) !== count($this->prices)) {
            throw new DomainException('Product must have all prices by active currencies');
        }

        foreach (Price\Currency::active() as $currency) {
            if (!$this->priceByCurrencyExists($currency)) {
                throw new DomainException('Product does not contain price for ' . $currency->value . ' currency');
            }
        }
    }

    private function priceByCurrencyExists(Price\Currency $currency): bool
    {
        foreach ($this->prices as $price) {
            if ($price->getCurrency() === $currency) {
                return true;
            }
        }

        return false;
    }

    private function normalizeColors()
    {
        $this->colors = array_unique($this->colors);
    }

    private function normalizeSizes()
    {
        $this->sizes = array_unique($this->sizes);
    }

    private function normalizePrices()
    {
        $prices = [];

        foreach ($this->prices as $price) {
            $price[$price->getCurrency->value] = $price;
        }

        $this->prices = $prices;
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

    public function getPrices(): array
    {
        return $this->prices;
    }


}