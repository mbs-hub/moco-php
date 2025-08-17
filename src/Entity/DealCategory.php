<?php

declare(strict_types=1);

namespace Moco\Entity;

use DateTime;

/**
 * @property int $id
 * @property string $name
 * @property int $probability
 * @property string $created_at
 * @property string $updated_at
 */
class DealCategory extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['name', 'probability'];
    }
}
