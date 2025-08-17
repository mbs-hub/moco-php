<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $type
 * @property string $title
 * @property string $description
 * @property double $quantity
 * @property string $unit
 * @property double $unit_price
 * @property double $net_total
 * @property double $unit_cost
 * @property bool $optional
 * @property bool $part
 * @property bool $additional
 * @property string $created_at
 * @property string $updated_at
 */
class CatalogServiceItem extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['type', 'title', 'net_total'];
    }
}
