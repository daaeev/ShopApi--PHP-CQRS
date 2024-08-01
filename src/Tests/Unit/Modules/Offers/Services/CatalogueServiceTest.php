<?php

namespace Project\Tests\Unit\Modules\Offers\Services;

use PHPUnit\Framework\TestCase;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Catalogue\Api\CatalogueApi;
use Project\Common\Services\Environment\Language;
use Project\Modules\Catalogue\Api\DTO\Product\Price;
use Project\Modules\Catalogue\Api\DTO\Product\Product;
use Project\Modules\Catalogue\Api\DTO\Product\Content;
use Project\Modules\Shopping\Adapters\CatalogueService;
use Project\Modules\Catalogue\Api\DTO\CatalogueProduct;
use Project\Modules\Catalogue\Api\DTO\Product\Settings;
use Project\Common\Services\Environment\EnvironmentInterface;

class CatalogueServiceTest extends TestCase
{
    private readonly CatalogueApi $catalogue;
    private readonly EnvironmentInterface $environment;
    private readonly CatalogueService $service;

    protected function setUp(): void
    {
        $this->catalogue = $this->getMockBuilder(CatalogueApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();
        $this->service = new CatalogueService($this->catalogue, $this->environment);
    }

    public function testResolveOffer()
    {
        $productId = rand(1, 10);
        $this->environment->expects($this->once())
            ->method('getLanguage')
            ->willReturn(Language::default());

        $catalogueProduct = $this->generateCatalogueProductDTO(
            productId: $productId,
            active: true,
            availability: Availability::IN_STOCK->value,
            withDefaultPrice: true,
        );

        $this->catalogue->expects($this->once())
            ->method('get')
            ->with($productId, Language::default())
            ->willReturn($catalogueProduct);

        $offer = $this->service->resolveOffer(
            $productId,
            $quantity = 2,
            Currency::default(),
            $size = 'XL',
            $color = 'Blue'
        );

        $this->assertNull($offer->getId()->getId());
        $this->assertNotNull($offer->getUuid()->getId());
        $this->assertSame($offer->getProduct(), $productId);
        $this->assertSame($offer->getName(), $catalogueProduct->product->name);
        $this->assertSame($offer->getPrice(), $catalogueProduct->product->prices[0]->price);
        $this->assertSame($offer->getRegularPrice(), $catalogueProduct->product->prices[0]->price);
        $this->assertSame($offer->getQuantity(), $quantity);
        $this->assertSame($offer->getSize(), $size);
        $this->assertSame($offer->getColor(), $color);
    }

    private function generateCatalogueProductDTO(
        int $productId,
        bool $active,
        string $availability,
        bool $withDefaultPrice,
    ): CatalogueProduct {
        return new CatalogueProduct(
            product: new Product(
                id: $productId,
                name: uniqid(),
                code: uniqid(),
                active: $active,
                availability: $availability,
                colors: ['Blue'],
                sizes: ['XL'],
                prices: $withDefaultPrice ? [new Price(Currency::default()->value, rand(100, 500))] : [],
                createdAt: new \DateTimeImmutable(),
                updatedAt: new \DateTimeImmutable(),
            ),
            content: $this->getMockBuilder(Content::class)
                ->disableOriginalConstructor()
                ->getMock(),
            preview: uniqid(),
            additionalImages: [],
            settings: $this->getMockBuilder(Settings::class)
                ->disableOriginalConstructor()
                ->getMock(),
            categories: [],
        );
    }

    public function testResolveOfferIfProductIsInactive()
    {
        $productId = rand(1, 10);
        $this->environment->expects($this->once())
            ->method('getLanguage')
            ->willReturn(Language::default());

        $catalogueProduct = $this->generateCatalogueProductDTO(
            productId: $productId,
            active: false,
            availability: Availability::IN_STOCK->value,
            withDefaultPrice: true,
        );

        $this->catalogue->expects($this->once())
            ->method('get')
            ->with($productId, Language::default())
            ->willReturn($catalogueProduct);

        $this->expectException(\DomainException::class);
        $this->service->resolveOffer(
            productId: $productId,
            quantity: 2,
            currency: Currency::default(),
            size: 'XL',
            color: 'Blue'
        );
    }

    public function testResolveOfferIfProductIsUnavailable()
    {
        $productId = rand(1, 10);
        $this->environment->expects($this->once())
            ->method('getLanguage')
            ->willReturn(Language::default());

        $catalogueProduct = $this->generateCatalogueProductDTO(
            productId: $productId,
            active: true,
            availability: Availability::OUT_STOCK->value,
            withDefaultPrice: true,
        );

        $this->catalogue->expects($this->once())
            ->method('get')
            ->with($productId, Language::default())
            ->willReturn($catalogueProduct);

        $this->expectException(\DomainException::class);
        $this->service->resolveOffer(
            productId: $productId,
            quantity: 2,
            currency: Currency::default(),
            size: 'XL',
            color: 'Blue'
        );
    }

    public function testResolveOfferIfProductDoesNotHaveSize()
    {
        $productId = rand(1, 10);
        $this->environment->expects($this->once())
            ->method('getLanguage')
            ->willReturn(Language::default());

        $catalogueProduct = $this->generateCatalogueProductDTO(
            productId: $productId,
            active: true,
            availability: Availability::IN_STOCK->value,
            withDefaultPrice: true,
        );

        $this->catalogue->expects($this->once())
            ->method('get')
            ->with($productId, Language::default())
            ->willReturn($catalogueProduct);

        $this->expectException(\DomainException::class);
        $this->service->resolveOffer(
            productId: $productId,
            quantity: 2,
            currency: Currency::default(),
            size: 'UNDEFINED',
            color: 'Blue'
        );
    }

    public function testResolveOfferIfProductDoesNotHaveColor()
    {
        $productId = rand(1, 10);
        $this->environment->expects($this->once())
            ->method('getLanguage')
            ->willReturn(Language::default());

        $catalogueProduct = $this->generateCatalogueProductDTO(
            productId: $productId,
            active: true,
            availability: Availability::IN_STOCK->value,
            withDefaultPrice: true,
        );

        $this->catalogue->expects($this->once())
            ->method('get')
            ->with($productId, Language::default())
            ->willReturn($catalogueProduct);

        $this->expectException(\DomainException::class);
        $this->service->resolveOffer(
            productId: $productId,
            quantity: 2,
            currency: Currency::default(),
            size: 'XL',
            color: 'UNDEFINED'
        );
    }

    public function testResolveOfferIfProductDoesNotHavePrice()
    {
        $productId = rand(1, 10);
        $this->environment->expects($this->once())
            ->method('getLanguage')
            ->willReturn(Language::default());

        $catalogueProduct = $this->generateCatalogueProductDTO(
            productId: $productId,
            active: true,
            availability: Availability::IN_STOCK->value,
            withDefaultPrice: false,
        );

        $this->catalogue->expects($this->once())
            ->method('get')
            ->with($productId, Language::default())
            ->willReturn($catalogueProduct);

        $this->expectException(\DomainException::class);
        $this->service->resolveOffer(
            productId: $productId,
            quantity: 2,
            currency: Currency::default(),
            size: 'XL',
            color: 'Blue'
        );
    }
}