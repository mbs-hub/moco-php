<?php

namespace Tests\Unit\Service\Purchases;

use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class PurchaseCategoriesServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 123,
        "name" => "Travel expenses",
        "credit_account" => "6640",
        "active" => true,
        "created_at" => "2018-10-17T09:33:46Z",
        "updated_at" => "2018-10-17T09:33:46Z"
    ];

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $categories = $this->mocoClient->purchaseCategories->get();
        $this->assertEquals(123, $categories[0]->id);
        $this->assertEquals("Travel expenses", $categories[0]->name);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $category = $this->mocoClient->purchaseCategories->get(123);
        $this->assertEquals("6640", $category->credit_account);
        $this->assertTrue($category->active);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->purchaseCategories->get(999);
    }

    public function testGetWithParams(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $categories = $this->mocoClient->purchaseCategories->get(['active' => true]);
        $this->assertEquals(123, $categories[0]->id);
    }
}
