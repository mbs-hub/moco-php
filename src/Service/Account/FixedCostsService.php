<?php

namespace Moco\Service\Account;

use Moco\Entity\FixedCosts;
use Moco\Entity\AbstractMocoEntity;
use Moco\Service\AbstractService;

class FixedCostsService extends AbstractService
{
    protected function getEndpoint(): string
    {
        return $this->endpoint . 'account/fixed_costs';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new FixedCosts();
    }

    public function get(array $params = null): AbstractMocoEntity|array|null
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
        } else {
            return $this->createMocoEntity($result, $this->getMocoObject());
        }
    }
}
