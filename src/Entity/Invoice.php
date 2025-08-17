<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $identifier
 * @property string $date
 * @property string $due_date
 * @property string $service_period_from
 * @property string $service_period_to
 * @property string $title
 * @property string $recipient_address
 * @property string $currency
 * @property float $net_total
 * @property float $tax
 * @property float $gross_total
 * @property string $discount
 * @property array $cash_discount
 * @property string $status
 * @property string $sent_on
 * @property string $paid_on
 * @property array $payments
 * @property array $reminders
 * @property bool $locked
 * @property string $notes
 * @property array $tags
 * @property array $custom_properties
 * @property array $customer
 * @property array $project
 * @property array $deal
 * @property array $company
 * @property array $items
 * @property string $created_at
 * @property string $updated_at
 */
class Invoice extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'customer_id',
            'recipient_address',
            'date',
            'due_date',
            'title',
            'tax',
            'currency'
        ];
    }
}
