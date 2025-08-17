<?php

declare(strict_types=1);

namespace Moco\Service\Offer;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Offer;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Get;
use Moco\Struct\Email;

/**
 * @method Offer create(array $params)
 * @method Offer|Offer[]|null get(int|array|null $params = null)
 */
class OffersService extends AbstractService
{
    use Create;
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'offers';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Offer();
    }

    public function getPdf(int $offerId, array $params = []): string
    {
        return $this->client->request(
            'GET',
            $this->getEndpoint() . '/' . $offerId . '.pdf' . $this->prepareQueryParams($params)
        );
    }

    public function assign(int $offerId, array $params): Offer
    {
        $params = $this->prepareParams($params);
        $result = $this->client->request('PUT', $this->getEndpoint() . '/' . $offerId . '/assign', $params);
        $result = json_decode($result);
        return $this->createMocoEntity($result, $this->getMocoObject());
    }

    public function updateStatus(int $offerId, string $status): void
    {
        $params = ['status' => $status];
        $params = $this->prepareParams($params);
        $this->client->request('PUT', $this->getEndpoint() . '/' . $offerId . '/update_status', $params);
    }

    public function sendEmail(int $offerId, array $params): Email
    {
        /** @var Offer $entity*/
        $entity = $this->getMocoObject();
        $this->validateParams($entity->getSendEmailMandatoryFields(), $params);
        $params = $this->prepareParams($params);
        $result = $this->client->request('POST', $this->getEndpoint() . '/' . $offerId . '/send_email', $params);
        $result = json_decode($result, true);
        return Email::fromArray($result);
    }

    public function getAttachments(int $offerId): array
    {
        $result = $this->client->request('GET', $this->getEndpoint() . '/' . $offerId . '/attachments');
        $result = json_decode($result);

        $attachments = [];
        if (is_array($result)) {
            foreach ($result as $attachment) {
                $attachments[] = (object) $attachment;
            }
        }
        return $attachments;
    }

    public function createAttachment(int $offerId, array $params): object
    {
        $params = $this->prepareParams($params);
        $result = $this->client->request('POST', $this->getEndpoint() . '/' . $offerId . '/attachments', $params);
        return json_decode($result);
    }

    public function deleteAttachment(int $offerId, int $attachmentId): void
    {
        $this->client->request('DELETE', $this->getEndpoint() . '/' . $offerId . '/attachments/' . $attachmentId);
    }
}
