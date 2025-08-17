<?php

namespace Moco\Entity;

/**
 * @property string $name
 */
class Tag extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
