<?php

namespace Tests\Unit\Service;

use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\InvalidResponseException;
use Moco\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\Unit\MocoClientTest;

abstract class AbstractServiceTest extends TestCase
{
    public MocoClientTest $mocoClient;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        HttpClientDiscovery::prependStrategy(MockClientStrategy::class);
        $this->mocoClient = new MocoClientTest(
            [
                'endpoint' => 'test',
                'token' => 'test'
            ]
        );
        parent::__construct($name, $data, $dataName);
    }

    public function mockResponse(int $expectedStatusCode, string $expectedReturn = '')
    {
        /**
* mock response
*/
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($expectedStatusCode);

        if (in_array($expectedStatusCode, range(200, 299))) {
            $streamInterface = $this->createMock(StreamInterface::class);
            $streamInterface->method('getContents')->willReturn($expectedReturn);
            $response->method('getBody')->willReturn($streamInterface);
            $this->mocoClient->getClient()->addResponse($response);
        } elseif (in_array($expectedStatusCode, range(400, 499))) {
            if ($expectedStatusCode === 404) {
                $exception =  new NotFoundException();
                $this->mocoClient->getClient()->addException($exception);
            }
            $exception =  new InvalidRequestException();
            $this->mocoClient->getClient()->addException($exception);
        } else {
            $exception =  new InvalidResponseException();
            $this->mocoClient->getClient()->addException($exception);
        }
    }

    public function test()
    {
        $this->assertTrue(true);
    }
}
