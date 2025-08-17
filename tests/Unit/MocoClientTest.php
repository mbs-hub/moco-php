<?php

namespace Tests\Unit;

use Moco\MocoClient;
use Psr\Http\Client\ClientInterface;

class MocoClientTest extends MocoClient
{
    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}
