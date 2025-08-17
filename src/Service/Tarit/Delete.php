<?php

namespace Moco\Service\Tarit;

trait Delete
{
    public function delete(int $id): void
    {
        $this->client->request("DELETE", $this->getEndPoint() . '/' . $id);
    }
}
