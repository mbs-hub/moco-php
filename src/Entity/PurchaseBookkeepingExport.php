<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $from
 * @property string $to
 * @property array $purchase_ids
 * @property string $comment
 * @property User $user
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class PurchaseBookkeepingExport extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'purchase_ids'
        ];
    }
}
