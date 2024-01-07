<?php

namespace Project\Tests\Unit\Modules\Categories\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\NotFoundException;
use Project\Common\CQRS\Buses\MessageBusInterface;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Categories\Commands\DeleteCategoryCommand;
use Project\Modules\Catalogue\Categories\Repository\CategoriesMemoryRepository;
use Project\Modules\Catalogue\Categories\Repository\CategoriesRepositoryInterface;
use Project\Modules\Catalogue\Categories\Commands\Handlers\DeleteCategoryHandler;

class DeleteCategoryTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, CategoryFactory;

    private CategoriesRepositoryInterface $categories;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->categories = new CategoriesMemoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->once()) // Category deleted
            ->method('dispatch');

        parent::setUp();
    }

    public function testDelete()
    {
        $initialCategory = $this->generateCategory();
        $this->categories->add($initialCategory);

        $command = new DeleteCategoryCommand(
            id: $initialCategory->getId()->getId(),
        );
        $handler = new DeleteCategoryHandler($this->categories);
        $handler->setDispatcher($this->dispatcher);
        call_user_func($handler, $command);

        $this->expectException(NotFoundException::class);
        $this->categories->get($initialCategory->getId());
    }
}