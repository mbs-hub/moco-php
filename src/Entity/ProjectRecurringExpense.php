<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $start_date
 * @property string $finish_date
 * @property string $recur_next
 * @property string $period
 * @property string $title
 * @property float $quantity
 * @property string $unit
 * @property float $unit_price
 * @property float $unit_cost
 * @property Project $project
 * @property bool $billable
 * @property bool $budget_relevant
 * @property string $service_period_to
 * @property string $service_period_from
 * @property string $service_period_direction
 * @property array $custom_properties
 */
class ProjectRecurringExpense extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'start_date',
            'period',
            'title',
            'quantity',
            'unit',
            'unit_price',
            'unit_cost'
        ];
    }
}
