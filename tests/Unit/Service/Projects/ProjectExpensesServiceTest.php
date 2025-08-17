<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Projects;

use Moco\Entity\ProjectExpense;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class ProjectExpensesServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 47266,
        "date" => "2024-06-07",
        "title" => "Hosting XS",
        "description" => "Monthly hosting service",
        "quantity" => 3.0,
        "unit" => "Monat",
        "unit_price" => 29.0,
        "unit_cost" => 25.0,
        "billable" => true,
        "billed" => false,
        "budget_relevant" => true,
        "company" => [
            "id" => 760269,
            "name" => "Beispiel AG"
        ],
        "project" => [
            "id" => 944514545,
            "name" => "Website Redesign"
        ],
        "user" => [
            "id" => 933590696,
            "firstname" => "John",
            "lastname" => "Doe"
        ],
        "custom_properties" => [],
        "service_period_from" => "2024-06-01",
        "service_period_to" => "2024-06-30",
        "created_at" => "2024-06-07T09:00:00Z",
        "updated_at" => "2024-06-07T09:00:00Z"
    ];

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetAll(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $expenses = $this->mocoClient->projectExpenses->get(['project_id' => 944514545]);
        $this->assertIsArray($expenses);
        $this->assertCount(1, $expenses);
        $this->assertInstanceOf(ProjectExpense::class, $expenses[0]);
        $this->assertEquals(47266, $expenses[0]->id);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $expense = $this->mocoClient->projectExpenses->get(['project_id' => 944514545, 'id' => 47266]);
        $this->assertInstanceOf(ProjectExpense::class, $expense);
        $this->assertEquals(47266, $expense->id);
        $this->assertEquals("2024-06-07", $expense->date);
        $this->assertEquals("Hosting XS", $expense->title);
        $this->assertEquals("Monthly hosting service", $expense->description);
        $this->assertEquals(3.0, $expense->quantity);
        $this->assertEquals("Monat", $expense->unit);
        $this->assertEquals(29.0, $expense->unit_price);
        $this->assertEquals(25.0, $expense->unit_cost);
        $this->assertTrue($expense->billable);
        $this->assertFalse($expense->billed);
        $this->assertTrue($expense->budget_relevant);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->projectExpenses->get(['project_id' => 944514545, 'id' => 999999]);
    }

    public function testCreate(): void
    {
        $createData = [
            'project_id' => 944514545,
            'date' => '2024-06-07',
            'title' => 'Hosting XS',
            'description' => 'Monthly hosting service',
            'quantity' => 3.0,
            'unit' => 'Monat',
            'unit_price' => 29.0,
            'unit_cost' => 25.0,
            'billable' => true,
            'budget_relevant' => true,
            'service_period_from' => '2024-06-01',
            'service_period_to' => '2024-06-30'
        ];

        $this->mockResponse(201, json_encode($this->expectedResponse));
        $expense = $this->mocoClient->projectExpenses->create($createData);
        $this->assertInstanceOf(ProjectExpense::class, $expense);
        $this->assertEquals(47266, $expense->id);
        $this->assertEquals("Hosting XS", $expense->title);
        $this->assertEquals(3.0, $expense->quantity);
        $this->assertEquals(29.0, $expense->unit_price);
    }

    public function testUpdate(): void
    {
        $updateData = [
            'project_id' => 944514545,
            'quantity' => 5.0,
            'unit_price' => 35.0,
            'description' => 'Updated hosting service'
        ];

        $updatedResponse = $this->expectedResponse;
        $updatedResponse['quantity'] = 5.0;
        $updatedResponse['unit_price'] = 35.0;
        $updatedResponse['description'] = 'Updated hosting service';

        $this->mockResponse(200, json_encode($updatedResponse));
        $expense = $this->mocoClient->projectExpenses->update(47266, $updateData);
        $this->assertInstanceOf(ProjectExpense::class, $expense);
        $this->assertEquals(47266, $expense->id);
        $this->assertEquals(5.0, $expense->quantity);
        $this->assertEquals(35.0, $expense->unit_price);
        $this->assertEquals('Updated hosting service', $expense->description);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->mocoClient->projectExpenses->delete(944514545, 47266);
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testCreateBulk(): void
    {
        $expensesData = [
            [
                'date' => '2024-06-07',
                'title' => 'Hosting XS',
                'quantity' => 1.0,
                'unit' => 'Stk',
                'unit_price' => 29.0,
                'unit_cost' => 25.0
            ],
            [
                'date' => '2024-06-07',
                'title' => 'Domain',
                'quantity' => 1.0,
                'unit' => 'Stk',
                'unit_price' => 15.0,
                'unit_cost' => 12.0
            ]
        ];

        $bulkResponse = [
            $this->expectedResponse,
            array_merge($this->expectedResponse, [
                'id' => 47267,
                'title' => 'Domain',
                'unit_price' => 15.0,
                'unit_cost' => 12.0
            ])
        ];

        $this->mockResponse(201, json_encode($bulkResponse));
        $expenses = $this->mocoClient->projectExpenses->createBulk(944514545, $expensesData);
        $this->assertIsArray($expenses);
        $this->assertCount(2, $expenses);
        $this->assertInstanceOf(ProjectExpense::class, $expenses[0]);
        $this->assertInstanceOf(ProjectExpense::class, $expenses[1]);
        $this->assertEquals(47266, $expenses[0]->id);
        $this->assertEquals(47267, $expenses[1]->id);
    }

    public function testDisregard(): void
    {
        $expenseIds = [47266, 47267];
        $reason = 'Already billed manually';

        $this->mockResponse(204);
        $this->mocoClient->projectExpenses->disregard(944514545, $expenseIds, $reason);
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testGetAllWithFilters(): void
    {
        $filters = [
            'project_id' => 944514545,
            'billable' => true,
            'billed' => false,
            'budget_relevant' => true
        ];

        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $expenses = $this->mocoClient->projectExpenses->get($filters);
        $this->assertIsArray($expenses);
        $this->assertCount(1, $expenses);
        $this->assertInstanceOf(ProjectExpense::class, $expenses[0]);
        $this->assertTrue($expenses[0]->billable);
        $this->assertFalse($expenses[0]->billed);
    }
}
