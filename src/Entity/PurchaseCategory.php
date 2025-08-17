<?php

namespace Moco\Entity;

use DateTime;

/**
 * @property int $id
 * @property string $name
 * @property string $credit_account
 * @property bool $active
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class PurchaseCategory extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
