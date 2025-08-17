<?php

namespace Moco\Service\Tarit;

use Moco\Entity\AbstractMocoEntity;

trait Update
{
    public function update(int $id, array $params): AbstractMocoEntity
    {
        $params = $this->prepareParams($params);
        $result = $this->client->request("PUT", $this->getEndPoint() . '/' . $id, $params);

        return $this->createMocoEntity(json_decode($result), $this->getMocoObject());
    }
}
