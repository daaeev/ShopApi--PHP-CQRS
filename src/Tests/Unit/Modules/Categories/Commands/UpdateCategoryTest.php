<?php

namespace Project\Tests\Unit\Modules\Categories\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Catalogue\Categories\Entity;
use Project\Common\CQRS\Buses\MessageBusInterface;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Categories\Commands\UpdateCategoryCommand;
use Project\Modules\Catalogue\Product\Repository\ProductsMemoryRepository;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;
use Project\Modules\Catalogue\Categories\Repository\CategoriesMemoryRepository;
use Project\Modules\Catalogue\Categories\Repository\CategoriesRepositoryInterface;
use Project\Modules\Catalogue\Categories\Commands\Handlers\UpdateCategoryHandler;

class UpdateCategoryTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, CategoryFactory;

    private CategoriesRepositoryInterface $categories;
    private ProductsRepositoryInterface $products;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new ProductsMemoryRepository(new Hydrator);
        $this->categories = new CategoriesMemoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->once()) // Category updated
            ->method('dispatch');

        parent::setUp();
    }

    public function testUpdate()
    {
        $initialCategory = $this->generateCategory();
        $this->categories->add($initialCategory);

        $parentCategory = $this->generateCategory();
        $categoryProduct = $this->generateProduct();
        $this->categories->add($parentCategory);
        $this->products->add($categoryProduct);

        $command = new UpdateCategoryCommand(
            id: $initialCategory->getId()->getId(),
            name: md5(rand()),
            slug: md5(rand()),
            products: [$categoryProduct->getId()->getId()],
            parent: $parentCategory->getId()->getId()
        );
        $handler = new UpdateCategoryHandler($this->categories, $this->products);
        $handler->setDispatcher($this->dispatcher);

        call_user_func($handler, $command);
        $category = $this->categories->get($initialCategory->getId());
        $this->assertSameCategory($category, $command);
    }

    private function assertSameCategory(Entity\Category $category, UpdateCategoryCommand $command): void
    {
        $this->assertSame($command->id, $category->getId()->getId());
        $this->assertSame($command->name, $category->getName());
        $this->assertSame($command->slug, $category->getSlug());
        $this->assertSame($command->parent, $category->getParent()?->getId());

        $this->assertCount(count($command->products), $category->getProducts());
        foreach ($command->products as $product) {
            $this->assertTrue(in_array($product, $category->getProducts()));
        }
    }
}