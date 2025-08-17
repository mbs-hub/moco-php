<?php

declare(strict_types=1);

namespace Moco\Service\Invoice;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\InvoicePayment;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method InvoicePayment create(array $params)
 * @method InvoicePayment|InvoicePayment[]|null get(int|array|null $params = null)
 * @method InvoicePayment update(int $id, array $params)
 * @method void delete(int $id)
 */
class InvoicePaymentsService extends AbstractService
{
    use Create;
    use Get;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'invoices/payments';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new InvoicePayment();
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
