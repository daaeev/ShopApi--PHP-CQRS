<?php

namespace Project\Tests\Unit\Modules\Promotions\Repository;

use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\Promotion;
use Project\Modules\Shopping\Discounts\Promotions\Entity\PromotionId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountMechanicFactoryInterface;

trait PromotionsRepositoryTestTrait
{
    use PromotionFactory;

    protected PromotionsRepositoryInterface $promotions;
    protected DiscountMechanicFactoryInterface $discountFactory;

    public function testAdd()
    {
        $initial = $this->generatePromotion();
        $initial->disable();
        $initial->addDiscount($this->generateDiscount());
        $initial->addDiscount($this->generateDiscount());
        $this->promotions->add($initial);
        $found = $this->promotions->get($initial->getId());
        $this->assertSamePromotions($initial, $found);
    }

    private function assertSamePromotions(Promotion $initial, Promotion $other): void
    {
        $this->assertTrue($initial->getId()->equalsTo($other->getId()));
        $this->assertSame($initial->getName(), $other->getName());
        $this->assertSame($initial->disabled(), $other->disabled());
        $this->assertSame($initial->isActive(), $other->isActive());
        $this->assertSame($initial->getActualStatus(), $other->getActualStatus());
        $this->assertSame(
            $initial->getStartDate()->getTimestamp(),
            $other->getStartDate()->getTimestamp()
        );
        $this->assertSame(
            $initial->getUpdatedAt()?->getTimestamp(),
            $other->getUpdatedAt()?->getTimestamp()
        );
        $this->assertSame(
            $initial->getCreatedAt()->getTimestamp(),
            $other->getCreatedAt()->getTimestamp()
        );
        $this->assertSame(
            $initial->getUpdatedAt()?->getTimestamp(),
            $other->getUpdatedAt()?->getTimestamp()
        );

        foreach ($initial->getDiscounts() as $initialDiscount) {
            foreach ($other->getDiscounts() as $otherDiscount) {
                if ($initialDiscount->getId()->equalsTo($otherDiscount->getId())) {
                    $this->assertSame($initialDiscount->getType(), $otherDiscount->getType());
                    $this->assertSame($initialDiscount->getData(), $otherDiscount->getData());
                }
            }
        }
    }

    public function testAddIncrementIds()
    {
        $id = PromotionId::next();
        $discount = $this->discountFactory->make(DiscountType::PERCENTAGE, ['percent' => 25]);
        $promotion = $this->makePromotion(
            $id,
            'Promotion slug',
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
            [$discount]
        );
        $this->promotions->add($promotion);
        $this->assertNotNull($id->getId());
        $this->assertNotNull($discount->getId()->getId());
    }

    public function testAddWithDuplicatedId()
    {
        $promotion = $this->generatePromotion();
        $promotionWithSameId = $this->makePromotion(
            $promotion->getId(),
            $promotion->getName(),
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        );
        $this->promotions->add($promotion);
        $this->expectException(DuplicateKeyException::class);
        $this->promotions->add($promotionWithSameId);
    }

    public function testUpdate()
    {
        $initial = $this->generatePromotion();
        $this->promotions->add($initial);

        $added = $this->promotions->get($initial->getId());
        $added->disable();
        $discount = $this->generateDiscount();
        $added->addDiscount($discount);
        $added->updateName(md5(rand()));
        $startDate = $added->getStartDate()->add(
            \DateInterval::createFromDateString('-1 day')
        );
        $endDate = $added->getEndDate()->add(
            \DateInterval::createFromDateString('+1 day')
        );
        $added->updateStartDate($startDate);
        $added->updateEndDate($endDate);
        $this->promotions->update($added);

        $updated = $this->promotions->get($initial->getId());
        $this->assertSamePromotions($added, $updated);
        $this->assertNotEquals($initial->getName(), $updated->getName());
        $this->assertNotEquals($initial->disabled(), $updated->disabled());
        $this->assertNotEquals($initial->getStartDate(), $updated->getStartDate());
        $this->assertNotEquals($initial->getEndDate(), $updated->getEndDate());
        $this->assertNotEquals($initial->getDiscounts(), $updated->getDiscounts());
        $this->assertNotEmpty($discount->getId()->getId());
    }

    public function testUpdateIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $promotion = $this->generatePromotion();
        $this->promotions->update($promotion);
    }

    public function testDelete()
    {
        $promotion = $this->generatePromotion();
        $this->promotions->add($promotion);
        $this->promotions->delete($promotion);
        $this->expectException(NotFoundException::class);
        $this->promotions->get($promotion->getId());
    }

    public function testDeleteIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $promotion = $this->generatePromotion();
        $this->promotions->delete($promotion);
    }

    public function testGet()
    {
        $promotion = $this->generatePromotion();
        $this->promotions->add($promotion);
        $found = $this->promotions->get($promotion->getId());
        $this->assertSamePromotions($promotion, $found);
    }

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->promotions->get(PromotionId::random());
    }
}