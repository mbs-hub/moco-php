<?php

namespace Moco\Service\Purchases;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\PurchasePayment;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method PurchasePayment create(array $params)
 * @method PurchasePayment|array|null get(int|array|null $params = null)
 * @method PurchasePayment update(int $id, array $params)
 * @method void delete(int $id)
 */
class PurchasePaymentsService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'purchases/payments';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new PurchasePayment();
    }

    public function createBulk(array $payments): array
    {
        // Validate and prepare each payment
        $preparedPayments = [];
        foreach ($payments as $payment) {
            $this->validateParams($this->getMocoObject()->getMandatoryFields(), $payment);
            $preparedPayments[] = $this->prepareParams($payment);
        }

        $params = ['bulk_data' => $preparedPayments];
        $result = $this->client->request('POST', $this->getEndpoint() . '/bulk', $params);
        $result = json_decode($result);

        $entities = [];
        if (is_array($result)) {
            foreach ($result as $entity) {
                $entities[] = $this->createMocoEntity($entity, $this->getMocoObject());
            }
        }
        return $entities;
    }
}
