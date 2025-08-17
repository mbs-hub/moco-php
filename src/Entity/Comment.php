<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property int $commentable_id
 * @property string $commentable_type
 * @property string $text
 * @property bool $manual
 * @property array $user
 * @property string $created_at
 * @property string $updated_at
 */
class Comment extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [
            'commentable_id',
            'commentable_type',
            'text'
        ];
    }
}
