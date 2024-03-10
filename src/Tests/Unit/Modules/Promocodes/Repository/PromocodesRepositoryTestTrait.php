<?php

namespace Project\Tests\Unit\Modules\Promocodes\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\Promocode;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;

trait PromocodesRepositoryTestTrait
{
    use PromocodeFactory;

    protected PromocodesRepositoryInterface $promocodes;

    public function testAdd()
    {
        $initial = $this->generatePromocode();
		$initialProperties = $this->getPromocodeProperties($initial);
        $this->promocodes->add($initial);

        $found = $this->promocodes->get($initial->getId());
		$this->assertSame($initial, $found);
		$this->assertSame($initialProperties, $this->getPromocodeProperties($found));
    }

	private function getPromocodeProperties(Promocode $promocode): array
	{
		$id = $promocode->getId();
		$name = $promocode->getName();
		$code = $promocode->getCode();
		$discountPercent = $promocode->getDiscountPercent();
		$active = $promocode->isActive();
		$startDate = $promocode->getStartDate();
		$endDate = $promocode->getEndDate();
		$createdAt = $promocode->getCreatedAt();
		$updatedAt = $promocode->getUpdatedAt();
		return [$id, $name, $code, $discountPercent, $active, $startDate, $endDate, $createdAt, $updatedAt];
	}

    public function testAddIncrementIds()
    {
        $promocode = $this->makePromocode(
            PromocodeId::next(),
            md5(rand()),
            md5(rand()),
            rand(1, 100),
            new \DateTimeImmutable('-1 day')
        );

        $this->promocodes->add($promocode);
        $this->assertNotNull($promocode->getId()->getId());
    }

    public function testAddWithDuplicatedId()
    {
        $promocode = $this->generatePromocode();
        $promocodeWithSameId = $this->makePromocode(
            $promocode->getId(),
            $promocode->getName(),
            'Unique promo-code',
            rand(1, 100),
            new \DateTimeImmutable('-1 day')
        );

        $this->promocodes->add($promocode);
        $this->expectException(DuplicateKeyException::class);
        $this->promocodes->add($promocodeWithSameId);
    }

    public function testAddWithNotUniqueCode()
    {
        $promocode = $this->generatePromocode();
        $promocodeWithNotUniqueCode = $this->makePromocode(
            PromocodeId::next(),
            md5(rand()),
            $promocode->getCode(),
            rand(1, 100),
            new \DateTimeImmutable('-1 day')
        );

        $this->promocodes->add($promocode);
        $this->expectException(DuplicateKeyException::class);
        $this->promocodes->add($promocodeWithNotUniqueCode);
    }

    public function testUpdate()
    {
        $initial = $this->generatePromocode();
		$initialProperties = $this->getPromocodeProperties($initial);
        $this->promocodes->add($initial);

        $added = $this->promocodes->get($initial->getId());
        $added->deactivate();
        $added->setName(md5(rand()));
        $startDate = $added->getStartDate()->add(\DateInterval::createFromDateString('-1 day'));
        $endDate = $added->getEndDate()->add(\DateInterval::createFromDateString('+1 day'));
        $added->setStartDate($startDate);
        $added->setEndDate($endDate);
		$addedProperties = $this->getPromocodeProperties($added);
        $this->promocodes->update($added);

        $updated = $this->promocodes->get($initial->getId());
        $this->assertSame($initial, $added);
        $this->assertSame($added, $updated);

		$updatedProperties = $this->getPromocodeProperties($updated);
		$this->assertNotSame($initialProperties, $addedProperties);
		$this->assertNotSame($initialProperties, $updatedProperties);
		$this->assertSame($addedProperties, $updatedProperties);
    }

    public function testUpdateIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $promocode = $this->generatePromocode();
        $this->promocodes->update($promocode);
    }

    public function testDelete()
    {
        $promocode = $this->generatePromocode();
        $this->promocodes->add($promocode);
        $this->promocodes->delete($promocode);
        $this->expectException(NotFoundException::class);
        $this->promocodes->get($promocode->getId());
    }

    public function testDeleteIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $promocode = $this->generatePromocode();
        $this->promocodes->delete($promocode);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->promocodes->get(PromocodeId::random());
    }

	public function testGetByCode()
	{
		$initial = $this->generatePromocode();
		$initialProperties = $this->getPromocodeProperties($initial);
		$this->promocodes->add($initial);

		$found = $this->promocodes->getByCode($initial->getCode());
		$this->assertSame($initial, $found);
		$this->assertSame($initialProperties, $this->getPromocodeProperties($found));
	}

	public function testGetByCodeIfDoesNotExists()
	{
		$this->expectException(NotFoundException::class);
		$this->promocodes->getByCode('test');
	}
}