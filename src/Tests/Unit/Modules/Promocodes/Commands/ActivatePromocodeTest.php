<?php

namespace Project\Tests\Unit\Modules\Promocodes\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\ActivatePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers\ActivatePromocodeHandler;

class ActivatePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory;

    private PromocodesRepositoryInterface $promocodes;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        parent::setUp();
    }

    public function testActivate()
    {
        $this->dispatcher->expects($this->exactly(1)) // promo updated
            ->method('dispatch');

        $initial = $this->generatePromocode();
        $initial->deactivate();
        $initial->flushEvents();
        $this->promocodes->add($initial);

        $command = new ActivatePromocodeCommand(
            $initial->getId()->getId(),
        );
        $handler = new ActivatePromocodeHandler($this->promocodes);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $promocode = $this->promocodes->get($initial->getId());
        $this->assertTrue($promocode->getActive());
        $this->assertTrue($promocode->isActive());
    }

    public function testActivatePromoIfAlreadyActive()
    {
        $initial = $this->generatePromocode();
        $this->promocodes->add($initial);

        $command = new ActivatePromocodeCommand(
            $initial->getId()->getId(),
        );
        $handler = new ActivatePromocodeHandler($this->promocodes);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}