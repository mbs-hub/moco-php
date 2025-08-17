<?php

declare(strict_types=1);

namespace Moco\Service\Purchases;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\PurchaseBookkeepingExport;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Get;

/**
 * @method PurchaseBookkeepingExport create(array $params)
 * @method PurchaseBookkeepingExport|PurchaseBookkeepingExport[]|null get(int|array|null $params = null)
 */
class PurchaseBookkeepingExportsService extends AbstractService
{
    use Create;
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'purchases/bookkeeping_exports';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new PurchaseBookkeepingExport();
    }
}
