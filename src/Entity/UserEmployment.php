<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property float $weekly_target_hours
 * @property array $pattern
 * @property string $from
 * @property string|null $to
 * @property User $user
 * @property string $created_at
 * @property string $updated_at
 */
class UserEmployment extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['user_id', 'pattern'];
    }
}
