<?php

namespace Moco\Service\Account;

use Moco\Entity\InternalHourlyRate;
use Moco\Entity\AbstractMocoEntity;
use Moco\Service\AbstractService;

class InternalHourlyRatesService extends AbstractService
{
    protected function getEndpoint(): string
    {
        return $this->endpoint . 'account/internal_hourly_rates';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new InternalHourlyRate();
    }

    /**
     * @param array|null $params
     *
     * @return InternalHourlyRate[]|null
     */
    public function get(array $params = null): array|null
    {
        $urlQuery = '';
        if (!is_null($params)) {
            $params = $this->prepareParams($params);
            $urlQuery = '?' . http_build_query($params);
        }

        $result = $this->client->request("GET", $this->getEndPoint() . $urlQuery);

        $result = json_decode($result);
        if (is_array($result)) {
            $entities = [];
            foreach ($result as $entity) {
                $entities[] = $this->createMocoEntity($entity, $this->getMocoObject());
            }

            return $entities;
        }
        return null;
    }

    public function update(array $params): bool
    {
        $params = $this->prepareParams($params);
        $this->validateParams($this->getMocoObject()->getMandatoryFields(), $params);
        $result = $this->client->request("PATCH", $this->getEndPoint() . '/', $params);

        $result = json_decode($result);
        return ($result->status === 'ok');
    }
}
