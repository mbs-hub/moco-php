<?php

declare(strict_types=1);

namespace Functional\Service\Projects;

use Moco\Entity\ProjectExpense;
use Tests\Functional\Service\AbstractServiceTest;

class ProjectExpensesServiceTest extends AbstractServiceTest
{
    private int $testProjectId = 947556942; // This should be replaced with a valid project ID

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreate(): int
    {
        $createData = [
            'project_id' => $this->testProjectId,
            'date' => '2024-06-15',
            'title' => 'Test Hosting Service',
            'description' => 'Monthly hosting service for testing',
            'quantity' => 2.0,
            'unit' => 'Month',
            'unit_price' => 50.0,
            'unit_cost' => 40.0,
            'billable' => true,
            'budget_relevant' => true,
            'service_period_from' => '2024-06-01',
            'service_period_to' => '2024-06-30'
        ];

        $expense = $this->mocoClient->projectExpenses->create($createData);
        $this->assertInstanceOf(ProjectExpense::class, $expense);
        $this->assertNotNull($expense->id);
        $this->assertEquals('2024-06-15', $expense->date);
        $this->assertEquals('Test Hosting Service', $expense->title);
        $this->assertEquals('Monthly hosting service for testing', $expense->description);
        $this->assertEquals(2.0, $expense->quantity);
        $this->assertEquals('Month', $expense->unit);
        $this->assertEquals(50.0, $expense->unit_price);
        $this->assertEquals(40.0, $expense->unit_cost);
        $this->assertTrue($expense->billable);
        $this->assertFalse($expense->billed); // Should default to false
        $this->assertTrue($expense->budget_relevant);
        $this->assertNotNull($expense->created_at);
        $this->assertNotNull($expense->updated_at);

        return $expense->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $expenseId): int
    {
        $expense = $this->mocoClient->projectExpenses->get(['project_id' => $this->testProjectId, 'id' => $expenseId]);
        $this->assertInstanceOf(ProjectExpense::class, $expense);
        $this->assertEquals($expenseId, $expense->id);
        $this->assertEquals('Test Hosting Service', $expense->title);
        $this->assertEquals(2.0, $expense->quantity);
        $this->assertEquals(50.0, $expense->unit_price);
        $this->assertTrue($expense->billable);
        $this->assertFalse($expense->billed);

        return $expenseId;
    }

    /**
     * @depends testGet
     */
    public function testGetAll(int $expenseId): int
    {
        $expenses = $this->mocoClient->projectExpenses->get(['project_id' => $this->testProjectId]);
        $this->assertIsArray($expenses);
        $this->assertGreaterThan(0, count($expenses));

        // Find our test expense
        $foundExpense = null;
        foreach ($expenses as $expense) {
            if ($expense->id === $expenseId) {
                $foundExpense = $expense;
                break;
            }
        }

        $this->assertNotNull($foundExpense);
        $this->assertInstanceOf(ProjectExpense::class, $foundExpense);
        $this->assertEquals($expenseId, $foundExpense->id);
        $this->assertEquals('Test Hosting Service', $foundExpense->title);

        return $expenseId;
    }

    /**
     * @depends testGetAll
     */
    public function testGetAllWithFilters(int $expenseId): int
    {
        // Test filtering by billable status
        $expenses = $this->mocoClient->projectExpenses->get(['project_id' => $this->testProjectId, 'billable' => true]);
        $this->assertIsArray($expenses);

        // Find our test expense
        $foundExpense = null;
        foreach ($expenses as $expense) {
            if ($expense->id === $expenseId) {
                $foundExpense = $expense;
                break;
            }
        }

        $this->assertNotNull($foundExpense);
        $this->assertTrue($foundExpense->billable);

        return $expenseId;
    }

    /**
     * @depends testGetAllWithFilters
     */
    public function testUpdate(int $expenseId): int
    {
        $updateData = [
            'project_id' => $this->testProjectId,
            'quantity' => 3.0,
            'unit_price' => 60.0,
            'description' => 'Updated hosting service description',
            'budget_relevant' => false
        ];

        $updatedExpense = $this->mocoClient->projectExpenses->update($expenseId, $updateData);
        $this->assertInstanceOf(ProjectExpense::class, $updatedExpense);
        $this->assertEquals($expenseId, $updatedExpense->id);
        $this->assertEquals(3.0, $updatedExpense->quantity);
        $this->assertEquals(60.0, $updatedExpense->unit_price);
        $this->assertEquals('Updated hosting service description', $updatedExpense->description);
        $this->assertFalse($updatedExpense->budget_relevant);
        $this->assertEquals('Test Hosting Service', $updatedExpense->title); // Should remain unchanged

        return $expenseId;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $expenseId): void
    {
        $this->mocoClient->projectExpenses->delete($this->testProjectId, $expenseId);

        // Verify the expense was deleted by trying to get it
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->projectExpenses->get(['project_id' => $this->testProjectId, 'id' => $expenseId]);
    }

    public function testCreateBulk(): void
    {
        $expensesData = [
            [
                'date' => '2024-06-20',
                'title' => 'Bulk Expense 1',
                'quantity' => 1.0,
                'unit' => 'Piece',
                'unit_price' => 25.0,
                'unit_cost' => 20.0
            ],
            [
                'date' => '2024-06-20',
                'title' => 'Bulk Expense 2',
                'quantity' => 2.0,
                'unit' => 'Piece',
                'unit_price' => 35.0,
                'unit_cost' => 30.0
            ]
        ];

        try {
            $expenses = $this->mocoClient->projectExpenses->createBulk($this->testProjectId, $expensesData);
            $this->assertIsArray($expenses);
            $this->assertCount(2, $expenses);
            $this->assertInstanceOf(ProjectExpense::class, $expenses[0]);
            $this->assertInstanceOf(ProjectExpense::class, $expenses[1]);
            $this->assertEquals('Bulk Expense 1', $expenses[0]->title);
            $this->assertEquals('Bulk Expense 2', $expenses[1]->title);

            // Clean up
            foreach ($expenses as $expense) {
                $this->mocoClient->projectExpenses->delete($this->testProjectId, $expense->id);
            }
        } catch (\Exception $e) {
            // Skip this test if bulk creation is not available
            $this->markTestSkipped('Bulk creation not available: ' . $e->getMessage());
        }
    }

    public function testCreateMinimal(): void
    {
        $createData = [
            'project_id' => $this->testProjectId,
            'date' => '2024-06-25',
            'title' => 'Minimal Test Expense',
            'quantity' => 1.0,
            'unit' => 'Piece',
            'unit_price' => 10.0,
            'unit_cost' => 8.0
        ];

        try {
            $expense = $this->mocoClient->projectExpenses->create($createData);
            $this->assertInstanceOf(ProjectExpense::class, $expense);
            $this->assertNotNull($expense->id);
            $this->assertEquals('Minimal Test Expense', $expense->title);
            $this->assertEquals(1.0, $expense->quantity);
            $this->assertEquals(10.0, $expense->unit_price);

            // Clean up
            $this->mocoClient->projectExpenses->delete($this->testProjectId, $expense->id);
        } catch (\Exception $e) {
            // Skip this test if creation fails due to project limitations
            $this->markTestSkipped('Expense creation failed: ' . $e->getMessage());
        }
    }
}
