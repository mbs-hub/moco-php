<?php

namespace Moco\Service\Purchases;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Purchase;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method Purchase create(array $params)
 * @method Purchase|array|null get(int|array|null $params = null)
 * @method Purchase update(int $id, array $params)
 * @method void delete(int $id)
 */
class PurchasesService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'purchases';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Purchase();
    }

    public function assignToProject(int $purchaseId, array $params): object
    {
        $result = $this->client->request(
            'POST',
            $this->getEndpoint() . '/' . $purchaseId . '/assign_to_project',
            $params
        );
        return json_decode($result);
    }

    public function updateStatus(int $purchaseId, string $status): void
    {
        $params = ['status' => $status];
        $this->client->request(
            'PATCH',
            $this->getEndpoint() . '/' . $purchaseId . '/update_status',
            $params
        );
    }
}
