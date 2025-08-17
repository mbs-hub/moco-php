<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $website
 * @property string $email
 * @property string $billing_email_cc
 * @property string $phone
 * @property string $fax
 * @property string $address
 * @property array $tags
 * @property User $user
 * @property string $info
 * @property array $custom_properties
 * @property string $vat_identifier
 * @property string $identifier
 * @property float $billing_tax
 * @property array $billing_vat
 * @property string $currency
 * @property string $country_code
 * @property string $english_correspondence_language
 * @property int $default_invoice_due_days
 * @property string $footer
 * @property array $projects
 * @property string $created_at
 * @property string $updated_at
 * @property int $debit_number
 */
class Company extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['name', 'type'];
    }
}
