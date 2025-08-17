<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $comment
 * @property User $user
 * @property Project $project
 * @property Deal $deal
 * @property string $starts_on
 * @property string $ends_on
 * @property float $hours_per_day
 * @property int $symbol
 * @property bool $tentative
 * @property string $created_at
 * @property string $updated_at
 */
class PlanningEntry extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'starts_on', 'ends_on', 'hours_per_day'
        ];
    }
}
