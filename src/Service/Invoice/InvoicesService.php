<?php

declare(strict_types=1);

namespace Moco\Service\Invoice;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Invoice;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Get;
use Moco\Struct\Email;

/**
 * @method Invoice create(array $params)
 * @method Invoice|Invoice[]|null get(int|array|null $params = null)
 */
class InvoicesService extends AbstractService
{
    use Create;
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'invoices';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Invoice();
    }

    public function getLocked(array $params = []): array
    {
        $result = $this->client->request(
            'GET',
            $this->getEndpoint() . '/locked' . $this->prepareQueryParams($params)
        );
        $result = json_decode($result);

        $entities = [];
        if (is_array($result)) {
            foreach ($result as $entity) {
                $entities[] = $this->createMocoEntity($entity, $this->getMocoObject());
            }
        }
        return $entities;
    }

    public function getPdf(int $invoiceId, array $params = []): string
    {
        return $this->client->request(
            'GET',
            $this->getEndpoint() . '/' . $invoiceId . '.pdf' . $this->prepareQueryParams($params)
        );
    }

    public function getTimesheet(int $invoiceId): array
    {
        $result = $this->client->request('GET', $this->getEndpoint() . '/' . $invoiceId . '/timesheet');
        $result = json_decode($result);

        $activities = [];
        if (is_array($result)) {
            foreach ($result as $activity) {
                $activities[] = (object) $activity;
            }
        }
        return $activities;
    }

    public function getExpenses(int $invoiceId): array
    {
        $result = $this->client->request('GET', $this->getEndpoint() . '/' . $invoiceId . '/expenses');
        $result = json_decode($result);

        $expenses = [];
        if (is_array($result)) {
            foreach ($result as $expense) {
                $expenses[] = (object) $expense;
            }
        }
        return $expenses;
    }

    public function updateStatus(int $invoiceId, string $status): void
    {
        $params = ['status' => $status];
        $params = $this->prepareParams($params);
        $result = $this->client->request(
            'PUT',
            $this->getEndpoint() . '/' . $invoiceId . '/update_status',
            $params
        );
    }

    public function sendEmail(int $invoiceId, array $params = []): Email
    {
        $params = $this->prepareParams($params);
        $result = $this->client->request(
            'POST',
            $this->getEndpoint() . '/' . $invoiceId . '/send_email',
            $params
        );
        $result = json_decode($result, true);
        return Email::fromArray($result);
    }

    public function getAttachments(int $invoiceId): array
    {
        $result = $this->client->request(
            'GET',
            $this->getEndpoint() . '/' . $invoiceId . '/attachments'
        );
        $result = json_decode($result);

        $attachments = [];
        if (is_array($result)) {
            foreach ($result as $attachment) {
                $attachments[] = (object) $attachment;
            }
        }
        return $attachments;
    }

    public function createAttachment(int $invoiceId, array $params): object
    {
        $params = $this->prepareParams($params);
        $result = $this->client->request(
            'POST',
            $this->getEndpoint() . '/' . $invoiceId . '/attachments',
            $params
        );
        return json_decode($result);
    }

    public function deleteAttachment(int $invoiceId, int $attachmentId): void
    {
        $this->client->request(
            'DELETE',
            $this->getEndpoint() . '/' . $invoiceId . '/attachments/' . $attachmentId
        );
    }

    public function delete(int $id, array $param): void
    {
        $this->client->request(
            "DELETE",
            $this->getEndPoint() . '/' . $id,
            $this->prepareParams($param)
        );
    }
}
