<?php

namespace Moco\Service\Tarit;

use Moco\Entity\AbstractMocoEntity;

trait Create
{
    public function create(array $params): AbstractMocoEntity
    {
        $this->validateParams($this->getMocoObject()->getMandatoryFields(), $params);
        $params = $this->prepareParams($params);
        $result = $this->client->request('POST', $this->getEndPoint(), $params);
        return $this->createMocoEntity(json_decode($result), $this->getMocoObject());
    }
}
