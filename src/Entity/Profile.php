<?php

declare(strict_types=1);

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $email
 * @property string $full_name
 * @property string $first_name
 * @property string $last_name
 * @property bool $active
 * @property bool $external
 * @property string $avatar_url
 * @property Unit $unit
 * @property string $created_at
 * @property string $updated_at
 */
class Profile extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
