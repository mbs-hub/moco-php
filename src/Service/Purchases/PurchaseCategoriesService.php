<?php

namespace Moco\Service\Purchases;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\PurchaseCategory;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Get;

/**
 * @method PurchaseCategory|array|null get(int|array|null $params = null)
 */
class PurchaseCategoriesService extends AbstractService
{
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'purchases/categories';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new PurchaseCategory();
    }
}
