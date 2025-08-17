<?php

namespace Tests\Functional\Service;

use Moco\MocoClient;
use PHPUnit\Framework\TestCase;

abstract class AbstractServiceTest extends TestCase
{
    public MocoClient $mocoClient;
    protected int $leaderId;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->mocoClient = new MocoClient(
            [
                'endpoint' => getenv('ENDPOINT'),
                'token' => getenv("TOKEN")
            ]
        );

        $this->leaderId = getenv('LEADER_ID');

        parent::__construct($name, $data, $dataName);
    }
}
