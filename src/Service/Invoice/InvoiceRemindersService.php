<?php

declare(strict_types=1);

namespace Moco\Service\Invoice;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\InvoiceReminder;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Struct\Email;

/**
 * @method InvoiceReminder create(array $params)
 * @method InvoiceReminder|InvoiceReminder[]|null get(int|array|null $params = null)
 * @method void delete(int $id)
 */
class InvoiceRemindersService extends AbstractService
{
    use Create;
    use Get;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'invoice_reminders';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new InvoiceReminder();
    }

    public function sendEmail(int $reminderId, array $params): Email
    {
        $entity = $this->getMocoObject();
        if (method_exists($entity, 'getSendEmailMandatoryFields')) {
            $this->validateParams($entity->getSendEmailMandatoryFields(), $params);
        }
        $params = $this->prepareParams($params);
        $result = $this->client->request('POST', $this->getEndpoint() . '/' . $reminderId . '/send_email', $params);
        $result = json_decode($result, true);
        return Email::fromArray($result);
    }
}
