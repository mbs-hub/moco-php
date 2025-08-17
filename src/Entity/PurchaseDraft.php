<?php

namespace Moco\Entity;

use DateTime;

/**
 * @property int $id
 * @property string $title
 * @property string $email_from
 * @property string $email_body
 * @property User $user
 * @property string|null $file_url
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class PurchaseDraft extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
