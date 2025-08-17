<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property array $costs
 * @property string $created_at
 * @property string $updated_at
 */
class FixedCosts extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
