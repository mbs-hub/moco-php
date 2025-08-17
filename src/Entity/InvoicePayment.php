<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $date
 * @property array $invoice
 * @property float $paid_total
 * @property string $currency
 * @property bool $partially_paid
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class InvoicePayment extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'date',
            'paid_total'
        ];
    }
}
