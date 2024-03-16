<?php

namespace Project\Tests\Unit\Modules\Promotions\Repository;

use Project\Common\Entity\Duration;
use Project\Common\Repository\NotFoundException;
use Project\Common\Repository\DuplicateKeyException;
use Project\Tests\Unit\Modules\Helpers\PromotionFactory;
use Project\Modules\Shopping\Discounts\Promotions\Entity\Promotion;
use Project\Modules\Shopping\Discounts\Promotions\Entity\PromotionId;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\DiscountType;
use Project\Modules\Shopping\Discounts\Promotions\Repository\PromotionsRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics\MechanicFactoryInterface;

trait PromotionsRepositoryTestTrait
{
    use PromotionFactory;

    protected PromotionsRepositoryInterface $promotions;
    protected MechanicFactoryInterface $discountFactory;

    public function testAdd()
    {
        $initial = $this->generatePromotion();
        $initial->disable();
        $initial->addDiscount($this->generateDiscount());

		$initialProperties = $this->getPromotionProperties($initial);
        $this->promotions->add($initial);

        $found = $this->promotions->get($initial->getId());
        $this->assertSame($initial, $found);
		$this->assertSame($initialProperties, $this->getPromotionProperties($found));
    }

	private function getPromotionProperties(Promotion $promotion): array
	{
		$id = $promotion->getId();
		$name = $promotion->getName();
		$duration = $promotion->getDuration();
		$disabled = $promotion->disabled();
		$discounts = $promotion->getDiscounts();
		$createdAt = $promotion->getCreatedAt();
		$updatedAt = $promotion->getUpdatedAt();
		return [$id, $name, $duration, $disabled, $discounts, $createdAt, $updatedAt];
	}

    public function testAddIncrementIds()
    {
        $id = PromotionId::next();
        $discount = $this->discountFactory->make(DiscountType::PERCENTAGE, ['percent' => 25]);
        $promotion = $this->makePromotion(
            id: $id,
            name: 'Promotion slug',
            startDate: new \DateTimeImmutable('-1 day'),
            endDate: new \DateTimeImmutable('+1 day'),
            discounts: [$discount]
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
		$initialProperties = $this->getPromotionProperties($initial);
        $this->promotions->add($initial);

        $added = $this->promotions->get($initial->getId());
        $added->disable();
        $added->addDiscount($this->generateDiscount());
        $added->updateName(md5(rand()));
        $startDate = $added->getDuration()->getStartDate()->add(
            \DateInterval::createFromDateString('-1 day')
        );
        $endDate = $added->getDuration()->getEndDate()->add(
            \DateInterval::createFromDateString('+1 day')
        );
        $added->updateDuration(new Duration($startDate, $endDate));
		$addedProperties = $this->getPromotionProperties($added);
        $this->promotions->update($added);

        $updated = $this->promotions->get($initial->getId());
		$updatedProperties = $this->getPromotionProperties($updated);
		$this->assertSame($initial, $added);
        $this->assertSame($added, $updated);
		$this->assertNotSame($initialProperties, $addedProperties);
		$this->assertNotSame($initialProperties, $updatedProperties);
		$this->assertSame($addedProperties, $updatedProperties);
    }

    public function testUpdateIfDoesNotExists()
    {
        $promotion = $this->generatePromotion();
		$this->expectException(NotFoundException::class);
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

    public function testGetIfDoesNotExists()
    {
        $this->expectException(NotFoundException::class);
        $this->promotions->get(PromotionId::random());
    }
}