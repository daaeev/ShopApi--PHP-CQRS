<?php

namespace Project\Tests\Unit\Modules\Promocodes\Commands;

use Project\Common\Repository\IdentityMap;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\DeletePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesMemoryRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodesRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers\DeletePromocodeHandler;

class DeletePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory;

    private PromocodesRepositoryInterface $promocodes;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promocodes = new PromocodesMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(1)) // promo deleted
            ->method('dispatch');
    }

    public function testDelete()
    {
        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $this->promocodes->add($promocode);

        $command = new DeletePromocodeCommand($promocode->getId()->getId());
        $handler = new DeletePromocodeHandler($this->promocodes);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
        $this->expectException(NotFoundException::class);
        $this->promocodes->get($promocode->getId());
    }
}