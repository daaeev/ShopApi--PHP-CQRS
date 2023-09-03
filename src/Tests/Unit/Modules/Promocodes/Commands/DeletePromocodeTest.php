<?php

namespace Project\Tests\Unit\Modules\Promocodes\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\DeletePromocodeCommand;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\MemoryPromocodesRepository;
use Project\Modules\Shopping\Discounts\Promocodes\Repository\PromocodeRepositoryInterface;
use Project\Modules\Shopping\Discounts\Promocodes\Commands\Handlers\DeletePromocodeHandler;

class DeletePromocodeTest extends \PHPUnit\Framework\TestCase
{
    use PromocodeFactory;

    private PromocodeRepositoryInterface $promocodes;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->promocodes = new MemoryPromocodesRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        parent::setUp();
    }

    public function testDelete()
    {
        $this->dispatcher->expects($this->exactly(1)) // promo deleted
            ->method('dispatch');

        $promocode = $this->generatePromocode();
        $promocode->deactivate();
        $promocode->flushEvents();
        $this->promocodes->add($promocode);

        $command = new DeletePromocodeCommand(
            $promocode->getId()->getId(),
        );
        $handler = new DeletePromocodeHandler($this->promocodes);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);
        $this->expectException(NotFoundException::class);
        $this->promocodes->get($promocode->getId());
    }

    public function testDeleteActivePromocode()
    {
        $promocode = $this->generatePromocode();
        $this->promocodes->add($promocode);

        $command = new DeletePromocodeCommand(
            $promocode->getId()->getId(),
        );
        $handler = new DeletePromocodeHandler($this->promocodes);
        $this->expectException(\DomainException::class);
        call_user_func($handler, $command);
    }
}