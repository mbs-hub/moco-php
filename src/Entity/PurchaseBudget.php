<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property int $year
 * @property string $title
 * @property bool $active
 * @property float $target
 * @property float $exhausted
 * @property float $planned
 * @property float $remaining
 */
class PurchaseBudget extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
