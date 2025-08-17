<?php

namespace Moco\Service\Purchases;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\PurchaseDraft;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Get;

/**
 * @method PurchaseDraft|array|null get(int|array|null $params = null)
 */
class PurchaseDraftsService extends AbstractService
{
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'purchases/drafts';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new PurchaseDraft();
    }

    public function getPdf(int $draftId): string
    {
        return $this->client->request(
            'GET',
            $this->getEndpoint() . '/' . $draftId . '.pdf'
        );
    }
}
