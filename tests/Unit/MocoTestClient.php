<?php

namespace Tests\Unit;

use Moco\MocoClient;
use Psr\Http\Client\ClientInterface;

class MocoTestClient extends MocoClient
{
    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}
