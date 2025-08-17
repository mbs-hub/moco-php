<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $gender
 * @property string $firstname,
 * @property string $lastname
 * @property string $title
 * @property string $job_position
 * @property string $mobile_phone
 * @property string $work_fax
 * @property string $work_email
 * @property string $work_address
 * @property string $home_email
 * @property string $home_address
 * @property string $birthday
 * @property string $info
 * @property string $avatar_url
 * @property array $tags
 * @property array $company
 * @property string $created_at
 * @property string $updated_at
 */
class Contact extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'firstname',
            'lastname',
            'gender'
        ];
    }
}
