<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $name
 * @property array $users
 * @property string $created_at
 * @property string $updated_at
 */
class Unit extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['name'];
    }
}
