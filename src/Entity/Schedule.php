<?php

namespace Moco\Entity;

use DateTime;

/**
 * @property int $id
 * @property string $date
 * @property string|null $comment
 * @property bool $am
 * @property bool $pm
 * @property array $assignment
 * @property User $user
 * @property int $absence_code
 * @property string|null $symbol
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class Schedule extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'date',
            'absence_code'
        ];
    }
}
