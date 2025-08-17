<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property bool $active
 * @property bool $extern
 * @property string $email
 * @property string $mobile_phone
 * @property string $work_phone
 * @property string $home_address
 * @property string $info
 * @property string $birthday
 * @property string $avatar_url
 * @property array $tags
 * @property array $custom_properties
 * @property Unit $unit
 * @property string $created_at
 * @property string $updated_at
 */
class User extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'firstname',
            'lastname',
            'email',
            'password',
            'unit_id',
        ];
    }
}
