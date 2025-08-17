<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $placeholder
 * @property string $placeholder_en
 * @property string $entity
 * @property string $kind
 * @property bool $print_on_invoice
 * @property bool $print_on_offer
 * @property bool $print_on_timesheet
 * @property bool $notification_enabled
 * @property array $defaults
 * @property string $updated_at
 * @property string $created_at
 */
class CustomProperty extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
