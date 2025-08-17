<?php

namespace Moco\Entity;

/**
 * @property User $user
 * @property float $total_vacation_days
 * @property float $used_vacation_days
 * @property float $planned_vacation_days
 * @property float $sickdays
 */
class Report extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
