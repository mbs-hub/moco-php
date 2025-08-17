<?php

declare(strict_types=1);

namespace Moco\Service\Offer;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\OfferCustomerApproval;
use Moco\Service\AbstractService;

class OfferCustomerApprovalService extends AbstractService
{
    protected function getEndpoint(): string
    {
        return $this->endpoint . 'offers';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new OfferCustomerApproval();
    }

    public function get(int $offerId): OfferCustomerApproval
    {
        $result = $this->client->request('GET', $this->getEndpoint() . '/' . $offerId . '/customer_approval');
        $result = json_decode($result);

        return $this->createMocoEntity($result, $this->getMocoObject());
    }

    public function activate(int $offerId): OfferCustomerApproval
    {
        $result = $this->client->request('POST', $this->getEndpoint() . '/' . $offerId . '/customer_approval/activate');
        $result = json_decode($result);

        return $this->createMocoEntity($result, $this->getMocoObject());
    }

    public function deactivate(int $offerId): OfferCustomerApproval
    {
        $result = $this->client->request(
            'POST',
            $this->getEndpoint() . '/' . $offerId . '/customer_approval/deactivate'
        );
        $result = json_decode($result);
        return $this->createMocoEntity($result, $this->getMocoObject());
    }
}
