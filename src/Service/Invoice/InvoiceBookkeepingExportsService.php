<?php

declare(strict_types=1);

namespace Moco\Service\Invoice;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\InvoiceBookkeepingExport;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Get;

/**
 * @method InvoiceBookkeepingExport create(array $params)
 * @method InvoiceBookkeepingExport|InvoiceBookkeepingExport[]|null get(int|array|null $params = null)
 */
class InvoiceBookkeepingExportsService extends AbstractService
{
    use Create;
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'invoices/bookkeeping_exports';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new InvoiceBookkeepingExport();
    }
}
