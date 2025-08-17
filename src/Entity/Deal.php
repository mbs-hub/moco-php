<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $currency
 * @property int $money
 * @property string $reminder_date
 * @property User $user
 * @property DealCategory $category
 * @property Company $company
 * @property array $person
 * @property string $info
 * @property string $status
 * @property string $closed_on
 * @property string $service_period_from
 * @property string $service_period_to
 * @property array $tags
 */
class Deal extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'name',
            'currency',
            'money',
            'reminder_date',
            'user_id',
            'deal_category_id'
        ];
    }
}
