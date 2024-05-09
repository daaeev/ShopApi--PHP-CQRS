<?php

namespace Project\Tests\Unit\Modules\Categories\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Project\Common\Repository\IdentityMap;
use Project\Modules\Catalogue\Categories\Entity;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Catalogue\Categories\Commands\CreateCategoryCommand;
use Project\Modules\Catalogue\Product\Repository\ProductsMemoryRepository;
use Project\Modules\Catalogue\Product\Repository\ProductsRepositoryInterface;
use Project\Modules\Catalogue\Categories\Repository\CategoriesMemoryRepository;
use Project\Modules\Catalogue\Categories\Commands\Handlers\CreateCategoryHandler;
use Project\Modules\Catalogue\Categories\Repository\CategoriesRepositoryInterface;

class CreateCategoryTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, CategoryFactory;

    private CategoriesRepositoryInterface $categories;
    private ProductsRepositoryInterface $products;
    private MessageBusInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new ProductsMemoryRepository(new Hydrator, new IdentityMap);
        $this->categories = new CategoriesMemoryRepository(new Hydrator, new IdentityMap);
        $this->dispatcher = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $this->dispatcher->expects($this->exactly(2)) // Category created, category updated
            ->method('dispatch');
    }

    public function testCreate()
    {
        $parentCategory = $this->generateCategory();
        $categoryProduct = $this->generateProduct();
        $this->categories->add($parentCategory);
        $this->products->add($categoryProduct);

        $command = new CreateCategoryCommand(
            name: md5(rand()),
            slug: md5(rand()),
            products: [$categoryProduct->getId()->getId()],
            parent: $parentCategory->getId()->getId()
        );

        $handler = new CreateCategoryHandler($this->categories, $this->products);
        $handler->setDispatcher($this->dispatcher);
        $categoryId = call_user_func($handler, $command);

        $category = $this->categories->get(new Entity\CategoryId($categoryId));
        $this->assertSameCategory($category, $command);
    }

    private function assertSameCategory(Entity\Category $category, CreateCategoryCommand $command): void
    {
        $this->assertSame($command->name, $category->getName());
        $this->assertSame($command->slug, $category->getSlug());
        $this->assertSame($command->parent, $category->getParent()?->getId());
        $this->assertSame($command->products, $category->getProducts());
    }
}
