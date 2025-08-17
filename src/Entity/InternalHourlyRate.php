<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $full_name
 * @property array $rates
 */
class InternalHourlyRate extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'year',
            'rates'
        ];
    }
}
