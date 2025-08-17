<?php

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Report;

class ReportsService extends AbstractService
{
    protected function getEndpoint(): string
    {
        return $this->endpoint . 'report';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Report();
    }

    public function getAbsences(array $params = []): array
    {
        $preparedParams = $this->prepareParams($params);
        $urlQuery = empty($preparedParams) ? '' : '?' . http_build_query($preparedParams);
        $result = $this->client->request('GET', $this->getEndpoint() . '/absences' . $urlQuery);

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
