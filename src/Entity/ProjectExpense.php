<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $date
 * @property string $title
 * @property string $description
 * @property float $quantity
 * @property string $unit
 * @property float $unit_price
 * @property float $unit_cost
 * @property bool $billable
 * @property bool $billed
 * @property bool $budget_relevant
 * @property object $company
 * @property object $project
 * @property object $user
 * @property array $custom_properties
 * @property string $service_period_from
 * @property string $service_period_to
 * @property string $created_at
 * @property string $updated_at
 */
class ProjectExpense extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'date', 'title', 'quantity', 'unit', 'unit_price', 'unit_cost'
        ];
    }
}
