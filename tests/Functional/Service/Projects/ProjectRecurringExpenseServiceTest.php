<?php

namespace Functional\Service\Projects;

use Moco\Entity\ProjectRecurringExpense;
use Tests\Functional\Service\AbstractServiceTest;

class ProjectRecurringExpenseServiceTest extends AbstractServiceTest
{
    public function testCreate(): array
    {
        $params = [
            'project_id' => 947556942,
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

        $recurringExpense = $this->mocoClient->projectRecurringExpense->create($params);
        $this->assertInstanceOf(ProjectRecurringExpense::class, $recurringExpense);
        $this->assertEquals('Hosting XS', $recurringExpense->title);
        $this->assertEquals(29.0, $recurringExpense->unit_price);
        return ['project_id' => 947556942, 'id' => $recurringExpense->id];
    }

    /**
     * @depends testCreate
     */
    public function testGet(array $data): array
    {
        $recurringExpenses = $this->mocoClient->projectRecurringExpense->get(['project_id' => $data['project_id']]);
        $this->assertIsArray($recurringExpenses);

        $recurringExpense = $this->mocoClient->projectRecurringExpense->get($data);
        $this->assertInstanceOf(ProjectRecurringExpense::class, $recurringExpense);
        $this->assertEquals('Hosting XS', $recurringExpense->title);
        return $data;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(array $data): array
    {
        $data = array_merge($data, ['unit_price' => 59.0, 'title' => 'Hosting M']);
        $recurringExpense = $this->mocoClient->projectRecurringExpense->update($data['id'], $data);
        $this->assertInstanceOf(ProjectRecurringExpense::class, $recurringExpense);
        $this->assertEquals(59.0, $recurringExpense->unit_price);
        $this->assertEquals('Hosting M', $recurringExpense->title);
        return $data;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(array $data): void
    {
        $this->assertNull($this->mocoClient->projectRecurringExpense->delete($data['project_id'], $data['id']));
    }
}
