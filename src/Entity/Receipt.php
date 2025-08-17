<?php

namespace Moco\Entity;

use DateTime;

/**
 * @property int $id
 * @property string $title
 * @property string $date
 * @property bool $billable
 * @property float $gross_total
 * @property string $currency
 * @property User $user
 * @property Project $project
 * @property array $items
 * @property array $refund_request
 * @property string|null $attachment_filename
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class Receipt extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'date',
            'title',
            'currency',
            'items'
        ];
    }
}
