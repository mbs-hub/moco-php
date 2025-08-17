<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $title
 * @property CatalogServiceItem[] $items
 */
class Catalog extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['title'];
    }
}
