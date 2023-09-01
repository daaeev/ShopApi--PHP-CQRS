<?php

namespace Project\Tests\Unit\Modules\Promocodes\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\Promocode;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodeRepositoryInterface;

trait PromocodesRepositoryTestTrait
{
    use PromocodeFactory;

    protected PromocodeRepositoryInterface $promocodes;

    public function testAdd()
    {
        $initial = $this->generatePromocode();
        $this->promocodes->add($initial);
        $found = $this->promocodes->get($initial->getId());
        $this->assertSamePromocodes($initial, $found);
    }

    private function assertSamePromocodes(Promocode $initial, Promocode $found): void
    {
        $this->assertTrue($initial->getId()->equalsTo($found->getId()));
        $this->assertSame($initial->getName(), $found->getName());
        $this->assertSame($initial->getCode(), $found->getCode());
        $this->assertSame($initial->getDiscountPercent(), $found->getDiscountPercent());
        $this->assertSame($initial->isActive(), $found->isActive());
        $this->assertEquals($initial->getStartDate(), $found->getStartDate());
        $this->assertEquals($initial->getEndDate(), $found->getEndDate());
        $this->assertEquals($initial->getCreatedAt(), $found->getCreatedAt());
        $this->assertEquals($initial->getUpdatedAt(), $found->getUpdatedAt());
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

        $this->assertNull($promocode->getId()->getId());
        $this->promocodes->add($promocode);
        $this->assertNotNull($promocode->getId()->getId());
    }

    public function testAddWithDuplicatedId()
    {
        $promocode = $this->generatePromocode();
        $duplicateIdPromocode = $this->makePromocode(
            $promocode->getId(),
            $promocode->getName(),
            'Unique promo-code',
            rand(1, 100),
            new \DateTimeImmutable('-1 day')
        );
        $this->promocodes->add($promocode);
        $this->expectException(DuplicateKeyException::class);
        $this->promocodes->add($duplicateIdPromocode);
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
        $this->promocodes->add($initial);

        $added = $this->promocodes->get($initial->getId());
        $added->setName(md5(rand()));
        $startDate = $added->getStartDate()->add(
            \DateInterval::createFromDateString('-1 day')
        );
        $endDate = $added->getEndDate()->add(
            \DateInterval::createFromDateString('+1 day')
        );
        $added->setStartDate($startDate);
        $added->setEndDate($endDate);
        $this->promocodes->update($added);

        $updated = $this->promocodes->get($initial->getId());
        $this->assertSamePromocodes($added, $updated);
        $this->assertNotEquals($initial->getName(), $updated->getName());
        $this->assertNotEquals($initial->getStartDate(), $updated->getStartDate());
        $this->assertNotEquals($initial->getEndDate(), $updated->getEndDate());
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

    public function testGet()
    {
        $promocode = $this->generatePromocode();
        $this->promocodes->add($promocode);
        $found = $this->promocodes->get($promocode->getId());
        $this->assertSamePromocodes($promocode, $found);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->promocodes->get(PromocodeId::random());
    }
}