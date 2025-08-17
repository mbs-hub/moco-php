<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $from
 * @property string $to
 * @property array $invoice_ids
 * @property string $comment
 * @property array $user
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class InvoiceBookkeepingExport extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'invoice_ids'
        ];
    }
}
