<?php

namespace Moco\Entity;

use DateTime;

/**
 * @property int $id
 * @property string $identifier
 * @property string $receipt_identifier
 * @property string $title
 * @property string|null $info
 * @property string $iban
 * @property string|null $reference
 * @property string $date
 * @property string|null $due_date
 * @property string $service_period_from
 * @property string $service_period_to
 * @property string $status
 * @property string $payment_method
 * @property float $net_total
 * @property float $gross_total
 * @property string $currency
 * @property string|null $file_url
 * @property array $custom_properties
 * @property array $tags
 * @property string $approval_status
 * @property Company $company
 * @property array $payments
 * @property User $user
 * @property array $refund_request
 * @property array $credit_card_transaction
 * @property array $items
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class Purchase extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'date',
            'currency',
            'payment_method',
            'items'
        ];
    }
}
