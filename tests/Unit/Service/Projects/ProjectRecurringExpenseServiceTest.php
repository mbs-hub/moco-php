<?php

namespace Tests\Unit\Service\Projects;

use Moco\Entity\ProjectRecurringExpense;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class ProjectRecurringExpenseServiceTest extends AbstractServiceTest
{
    public function testCreate(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'start_date' => '2024-01-01',
            'period' => 'monthly',
            'title' => 'Hosting XS',
            'quantity' => 1.0,
            'unit' => 'month',
            'unit_price' => 29.0,
            'unit_cost' => 25.0,
            'billable' => true,
            'budget_relevant' => true
        ];
        $this->mockResponse(200, json_encode($params));
        $recurringExpense = $this->mocoClient->projectRecurringExpense->create($params);
        $this->assertInstanceOf(ProjectRecurringExpense::class, $recurringExpense);
        $this->assertEquals('Hosting XS', $recurringExpense->title);

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->projectRecurringExpense->create([]);
    }

    public function testGet(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'start_date' => '2024-01-01',
            'period' => 'monthly',
            'title' => 'Hosting XS',
            'quantity' => 1.0,
            'unit' => 'month',
            'unit_price' => 29.0,
            'unit_cost' => 25.0,
            'billable' => true,
            'budget_relevant' => true
        ];

        $this->mockResponse(200, json_encode([$params]));
        $recurringExpenses = $this->mocoClient->projectRecurringExpense->get(['project_id' => 123]);
        $this->assertIsArray($recurringExpenses);

        $this->mockResponse(200, json_encode($params));
        $recurringExpense = $this->mocoClient->projectRecurringExpense->get(['project_id' => 123, 'id' => 1234]);
        $this->assertInstanceOf(ProjectRecurringExpense::class, $recurringExpense);
        $this->assertEquals('Hosting XS', $recurringExpense->title);

        $this->mockResponse(404, '');
        $this->expectException(NotFoundException::class);
        $this->mocoClient->projectRecurringExpense->get(['project_id' => 12345]);
    }

    public function testUpdate(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'start_date' => '2024-01-01',
            'period' => 'monthly',
            'title' => 'Hosting M',
            'quantity' => 1.0,
            'unit' => 'month',
            'unit_price' => 59.0,
            'unit_cost' => 45.0,
            'billable' => true,
            'budget_relevant' => true
        ];
        $this->mockResponse(200, json_encode($params));
        $recurringExpense = $this->mocoClient->projectRecurringExpense->update($params['id'], $params);
        $this->assertInstanceOf(ProjectRecurringExpense::class, $recurringExpense);
        $this->assertEquals('Hosting M', $recurringExpense->title);
        $this->assertEquals(59.0, $recurringExpense->unit_price);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->projectRecurringExpense->delete(123, 1234));
    }
}
