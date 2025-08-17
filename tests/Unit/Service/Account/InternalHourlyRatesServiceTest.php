<?php

namespace Tests\Unit\Service\Account;

use Moco\Exception\InvalidRequestException;
use Tests\Unit\Service\AbstractServiceTest;

class InternalHourlyRatesServiceTest extends AbstractServiceTest
{
    public function testGet(): void
    {
        $mockedResult = [
            [
                "id"        => 933589613,
                "full_name" => "Daniela Demo",
                "rates"     => [
                    [
                        "year" => 2020,
                        "rate" => 120.0,
                    ],
                    [
                        "year" => 2021,
                        "rate" => 130.0,
                    ],
                ],
            ],
            [
                "id"        => 933618769,
                "full_name" => "Max Muster",
                "rates"     => [
                    [
                        "year" => 2020,
                        "rate" => 110.0,
                    ],
                    [
                        "year" => 2021,
                        "rate" => 120.0,
                    ],
                ],
            ],
        ];

        $this->mockResponse(200, json_encode($mockedResult));
        $result = $this->mocoClient->account->internalHourlyRates->get();
        $this->assertIsArray($result);

        $this->assertEquals(933589613, $result[0]->id);
    }

    public function testUpdate(): void
    {
        $updateParams = [
            'year' => 2022,
            'rates' => [
                [
                    'user_id' => 933736920,
                    'rate' => 140.0
                ]
            ]
        ];
        $this->mockResponse(200, json_encode(['status' => 'ok']));
        $result = $this->mocoClient->account->internalHourlyRates->update($updateParams);

        $this->assertTrue($result);

        $this->expectException(InvalidRequestException::class);
        unset($updateParams['year']);
        $this->mocoClient->account->internalHourlyRates->update($updateParams);
    }
}
