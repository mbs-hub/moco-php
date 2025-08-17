<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $name
 * @property bool $billable
 * @property bool $active
 * @property float $budget
 * @property float $hourly_rate
 * @property string $created_at
 * @property string $updated_at
 */
class ProjectTask extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['name'];
    }
}
