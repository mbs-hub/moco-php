<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $date
 * @property string $from
 * @property string $to
 * @property bool $is_home_office
 * @property User $user
 * @property string $created_at
 * @property string $updated_at
 */
class UserPresence extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['date', 'from'];
    }
}
