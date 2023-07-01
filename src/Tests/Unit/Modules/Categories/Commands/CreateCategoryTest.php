<?php

namespace Project\Tests\Unit\Modules\Categories\Commands;

use Project\Common\Entity\Hydrator\Hydrator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Categories\Commands\CreateCategoryCommand;
use Project\Modules\Catalogue\Product\Repository\MemoryProductRepository;
use Project\Modules\Catalogue\Product\Repository\ProductRepositoryInterface;
use Project\Modules\Catalogue\Categories\Repository\MemoryCategoryRepository;
use Project\Modules\Catalogue\Categories\Repository\CategoryRepositoryInterface;
use Project\Modules\Catalogue\Categories\Commands\Handlers\CreateCategoryHandler;
use Project\Modules\Catalogue\Categories\Entity;

class CreateCategoryTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, CategoryFactory;

    private CategoryRepositoryInterface $categories;
    private ProductRepositoryInterface $products;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        $this->products = new MemoryProductRepository(new Hydrator);
        $this->categories = new MemoryCategoryRepository(new Hydrator);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();
        $this->dispatcher->expects($this->exactly(2)) // Category created, category updated
        ->method('dispatch');
        parent::setUp();
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

        $this->assertCount(count($command->products), $category->getProducts());
        foreach ($command->products as $product) {
            $this->assertTrue(in_array($product, $category->getProducts()));
        }
    }
}