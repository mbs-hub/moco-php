<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $name
 * @property User $user
 * @property Company $company
 * @property float $budget
 * @property string $currency
 * @property string $info
 * @property array $custom_properties
 * @property string $customer_report_url
 * @property array $projects
 * @property string $created_at
 * @property string $updated_at
 */
class ProjectGroup extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['name'];
    }
}
