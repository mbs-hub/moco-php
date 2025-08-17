<?php

declare(strict_types=1);

namespace Moco\Service\Purchases;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\PurchaseBudget;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Get;

/**
 * @method PurchaseBudget|PurchaseBudget[]|null get(int|array|null $params = null)
 */
class PurchaseBudgetsService extends AbstractService
{
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'purchases/budgets';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new PurchaseBudget();
    }
}
