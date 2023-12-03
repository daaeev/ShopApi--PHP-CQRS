<?php

namespace Project\Tests\Unit\Modules\Categories\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\CategoryFactory;
use Project\Modules\Catalogue\Api\Events\Category\CategoryDeleted;

class DeleteCategoryTest extends \PHPUnit\Framework\TestCase
{
    use CategoryFactory, AssertEvents;

    public function testDelete()
    {
        $category = $this->generateCategory();
        $category->delete();
        $this->assertEvents($category, [new CategoryDeleted($category)]);
    }
}