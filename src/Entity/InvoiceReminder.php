<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $title
 * @property string $text
 * @property float $fee
 * @property string $date
 * @property string $due_date
 * @property array $invoice
 * @property string $created_at
 * @property string $updated_at
 */
class InvoiceReminder extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'invoice_id'
        ];
    }

    public function getSendEmailMandatoryFields(): array
    {
        return [
            'subject',
            'text'
        ];
    }
}
