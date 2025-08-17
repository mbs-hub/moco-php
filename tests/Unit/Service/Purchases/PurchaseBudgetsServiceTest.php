<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Purchases;

use Moco\Entity\PurchaseBudget;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class PurchaseBudgetsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 2,
        "year" => 2024,
        "title" => "Betrieb – Hosting",
        "active" => true,
        "target" => 84000.0,
        "exhausted" => 66483.0,
        "planned" => 4494.0,
        "remaining" => 13023.0
    ];

    public function testGetWithYearParameter(): void
    {
        $params = ['year' => 2024];

        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $budgets = $this->mocoClient->purchaseBudgets->get($params);
        $this->assertIsArray($budgets);
        $this->assertEquals(2, $budgets[0]->id);
        $this->assertEquals(2024, $budgets[0]->year);
        $this->assertEquals("Betrieb – Hosting", $budgets[0]->title);
        $this->assertTrue($budgets[0]->active);
        $this->assertEquals(84000.0, $budgets[0]->target);
    }

    public function testGetWithoutYear(): void
    {
        // Test that the service still works without year parameter (though API requires it)
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $budgets = $this->mocoClient->purchaseBudgets->get();
        $this->assertIsArray($budgets);
        $this->assertEquals(2, $budgets[0]->id);
    }

    public function testGetNotFound(): void
    {
        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->purchaseBudgets->get(['year' => 1999]);
    }

    public function testGetSingleBudget(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $budget = $this->mocoClient->purchaseBudgets->get(2);
        $this->assertInstanceOf(PurchaseBudget::class, $budget);
        $this->assertEquals(2024, $budget->year);
        $this->assertEquals(13023.0, $budget->remaining);
    }
}
