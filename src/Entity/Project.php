<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $identifier
 * @property string $name
 * @property bool $active
 * @property bool $billable
 * @property bool $fixed_price
 * @property bool $retainer
 * @property \DateTime $start_date
 * @property \DateTime $finish_date
 * @property string $color
 * @property string $currency
 * @property string $billing_variant
 * @property string $billing_address
 * @property string $billing_email_to
 * @property string $billing_email_cc
 * @property string $billing_notes
 * @property bool $setting_include_time_report
 * @property float $budget
 * @property float $budget_monthly
 * @property float $budget_expenses
 * @property float $hourly_rate
 * @property string $info
 * @property array $tags
 * @property array $custom_properties
 * @property array $leader
 * @property int $co_leader_id
 * @property array $customer
 * @property array $deal
 * @property array $tasks
 * @property array $contracts
 */
class Project extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'name',
            'currency',
            'leader_id',
            'customer_id'
        ];
    }
}
