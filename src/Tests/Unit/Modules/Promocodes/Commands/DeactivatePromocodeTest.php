<?php

namespace Project\Tests\Unit\Modules\Promocodes\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\DeactivatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers\DeactivatePromocodeHandler;

class DeactivatePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory;

    private PromocodesRepositoryInterface $promocodes;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        parent::setUp();
    }

    public function testDeactivate()
    {
        $this->dispatcher->expects($this->exactly(1)) // promo updated
            ->method('dispatch');

        $initial = $this->generatePromocode();
        $this->promocodes->add($initial);

        $command = new DeactivatePromocodeCommand(
            $initial->getId()->getId(),
        );
        $handler = new DeactivatePromocodeHandler($this->promocodes);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $promocode = $this->promocodes->get($initial->getId());
        $this->assertFalse($promocode->getActive());
        $this->assertFalse($promocode->isActive());
    }

    public function testDeactivatePromoIfAlreadyDeactivated()
    {
        $initial = $this->generatePromocode();
        $initial->deactivate();
        $this->promocodes->add($initial);

        $command = new DeactivatePromocodeCommand(
            $initial->getId()->getId(),
        );
        $handler = new DeactivatePromocodeHandler($this->promocodes);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}