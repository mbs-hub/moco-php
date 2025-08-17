<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $identifier
 * @property string $date
 * @property string $due_date
 * @property string $title
 * @property string $recipient_address
 * @property string $currency
 * @property float $net_total
 * @property float $tax
 * @property float $gross_total
 * @property string $discount
 * @property string $status
 * @property array $tags
 * @property array $custom_properties
 * @property array $company
 * @property array $project
 * @property array $deal
 * @property array $contact
 * @property array $items
 * @property string $notes
 * @property string $created_at
 * @property string $updated_at
 */
class Offer extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'recipient_address',
            'date',
            'due_date',
            'title',
            'tax',
            'items'
        ];
    }

    public function getSendEmailMandatoryFields(): array
    {
        return [
            'emails_to',
            'subject',
            'text'
        ];
    }
}
