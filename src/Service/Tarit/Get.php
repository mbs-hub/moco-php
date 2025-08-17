<?php

namespace Moco\Service\Tarit;

use Moco\Entity\AbstractMocoEntity;

trait Get
{
    /**
     * @psalm-suppress InvalidReturnType
     */
    public function get(int|array|null $params = null): AbstractMocoEntity|array|null
    {
        if (is_array($params)) {
            $params = $this->prepareParams($params);
            $urlQuery = '?' . http_build_query($params);
            $result = $this->client->request("GET", $this->getEndPoint() . $urlQuery);
        } else {
            $result = $this->client->request("GET", $this->getEndPoint() . '/' . $params);
        }

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
