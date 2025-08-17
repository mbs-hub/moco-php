<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $date
 * @property string $title
 * @property float $net_total
 * @property Project $project
 * @property bool $checked
 * @property bool $billed
 */
class ProjectPaymentSchedule extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'net_total',
            'date'
        ];
    }
}
