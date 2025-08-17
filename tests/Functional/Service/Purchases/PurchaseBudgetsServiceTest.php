<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Purchases;

use Moco\Entity\PurchaseBudget;
use Tests\Functional\Service\AbstractServiceTest;

class PurchaseBudgetsServiceTest extends AbstractServiceTest
{
    public function testGetCurrentYear(): void
    {
        $currentYear = date('Y');
        $budgets = $this->mocoClient->purchaseBudgets->get(['year' => (int)$currentYear]);

        $this->assertIsArray($budgets);
        foreach ($budgets as $budget) {
            $this->assertInstanceOf(PurchaseBudget::class, $budget);
            $this->assertEquals((int)$currentYear, $budget->year);
            $this->assertIsNumeric($budget->target);
            $this->assertIsNumeric($budget->exhausted);
            $this->assertIsNumeric($budget->planned);
            $this->assertIsNumeric($budget->remaining);
        }
    }

    public function testGetPreviousYear(): void
    {
        $previousYear = date('Y') - 1;
        $budgets = $this->mocoClient->purchaseBudgets->get(['year' => $previousYear]);

        $this->assertIsArray($budgets);
        foreach ($budgets as $budget) {
            $this->assertInstanceOf(PurchaseBudget::class, $budget);
            $this->assertEquals($previousYear, $budget->year);
        }
    }

    public function testGetAllBudgets(): void
    {
        // Test getting budgets without specific year parameter
        $budgets = $this->mocoClient->purchaseBudgets->get();

        $this->assertIsArray($budgets);
        foreach ($budgets as $budget) {
            $this->assertInstanceOf(PurchaseBudget::class, $budget);
            $this->assertNotEmpty($budget->title);
            $this->assertIsBool($budget->active);
        }
    }

    public function testBudgetAttributes(): void
    {
        $budgets = $this->mocoClient->purchaseBudgets->get(['year' => date('Y')]);

        if (!empty($budgets)) {
            $budget = $budgets[0];
            $this->assertInstanceOf(PurchaseBudget::class, $budget);
            $this->assertIsInt($budget->id);
            $this->assertIsInt($budget->year);
            $this->assertIsString($budget->title);
            $this->assertIsBool($budget->active);
            $this->assertIsFloat($budget->target);
            $this->assertIsFloat($budget->exhausted);
            $this->assertIsFloat($budget->planned);
            $this->assertIsFloat($budget->remaining);
        } else {
            $this->markTestSkipped('No budgets available for current year');
        }
    }
}
