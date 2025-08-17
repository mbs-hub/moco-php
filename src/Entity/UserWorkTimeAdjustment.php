<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $date
 * @property string $description
 * @property float $hours
 * @property User $creator
 * @property User $user
 * @property string $created_at
 * @property string $updated_at
 */
class UserWorkTimeAdjustment extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['user_id', 'description', 'date', 'hours'];
    }
}
