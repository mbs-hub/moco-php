<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $approval_url
 * @property string $offer_document_url
 * @property bool $active
 * @property string|null $customer_full_name
 * @property string|null $customer_email
 * @property string|null $signature_url
 * @property string|null $signed_at
 * @property string $created_at
 * @property string $updated_at
 */
class OfferCustomerApproval extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
