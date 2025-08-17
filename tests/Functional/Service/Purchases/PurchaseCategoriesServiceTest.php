<?php

namespace Functional\Service\Purchases;

use Moco\Entity\PurchaseCategory;
use Tests\Functional\Service\AbstractServiceTest;

class PurchaseCategoriesServiceTest extends AbstractServiceTest
{
    public function testGet(): void
    {
        $categories = $this->mocoClient->purchaseCategories->get();
        $this->assertIsArray($categories);

        if (count($categories) > 0) {
            $this->assertInstanceOf(PurchaseCategory::class, $categories[0]);
            $this->assertIsInt($categories[0]->id);
            $this->assertIsString($categories[0]->name);

            // Test getting single category
            $singleCategory = $this->mocoClient->purchaseCategories->get($categories[0]->id);
            $this->assertInstanceOf(PurchaseCategory::class, $singleCategory);
            $this->assertEquals($categories[0]->id, $singleCategory->id);
        }
    }

    public function testGetWithParams(): void
    {
        $activeCategories = $this->mocoClient->purchaseCategories->get(['active' => true]);
        $this->assertIsArray($activeCategories);

        foreach ($activeCategories as $category) {
            $this->assertInstanceOf(PurchaseCategory::class, $category);
            if (isset($category->active)) {
                $this->assertTrue($category->active);
            }
        }
    }
}
