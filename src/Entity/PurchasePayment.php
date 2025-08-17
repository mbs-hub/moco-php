<?php

namespace Moco\Entity;

use DateTime;

/**
 * @property int $id
 * @property string $date
 * @property Purchase $purchase
 * @property float $total
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class PurchasePayment extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'date',
            'total'
        ];
    }
}
