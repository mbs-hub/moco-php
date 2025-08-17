<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property int $user_id
 * @property string $firstname
 * @property string $lastname
 * @property bool $billable
 * @property bool $active
 * @property float $budget
 * @property float $hourly_rate
 * @property string $created_at
 * @property string $updated_at
 */
class ProjectContract extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'user_id'
        ];
    }
}
