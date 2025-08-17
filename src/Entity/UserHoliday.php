<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property int $year
 * @property string $title
 * @property float $days
 * @property float $hours
 * @property User $user
 * @property User $creator
 * @property string $created_at
 * @property string $updated_at
 */
class UserHoliday extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['year', 'title', 'days', 'user_id'];
    }
}
