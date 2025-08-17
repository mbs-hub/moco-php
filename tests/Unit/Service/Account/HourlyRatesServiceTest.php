<?php

namespace Tests\Unit\Service\Account;

use Tests\Unit\Service\AbstractServiceTest;

class HourlyRatesServiceTest extends AbstractServiceTest
{
    public function testGet(): void
    {
        $mockHourlyRates = [
            "defaults_rates" => [
                ["currency" => "CHF", "hourly_rate" => 160.0],
                ["currency" => "EUR", "hourly_rate" => 150.0],
                ["currency" => "USD", "hourly_rate" => 160.0],
            ],
            "tasks"          => [
                [
                    "id"    => 5004,
                    "name"  => "Grafik",
                    "rates" => [
                        ["currency" => "CHF", "hourly_rate" => 160.0],
                        ["currency" => "EUR", "hourly_rate" => 150.0],
                        ["currency" => "USD", "hourly_rate" => 160.0],
                    ],
                ],
                [
                    "id"    => 5005,
                    "name"  => "Entwicklung",
                    "rates" => [
                        ["currency" => "CHF", "hourly_rate" => 160.0],
                        ["currency" => "EUR", "hourly_rate" => 150.0],
                        ["currency" => "USD", "hourly_rate" => 160.0],
                    ],
                ],
                [
                    "id"    => 5006,
                    "name"  => "Projektleitung",
                    "rates" => [
                        ["currency" => "CHF", "hourly_rate" => 170.0],
                        ["currency" => "EUR", "hourly_rate" => 160.0],
                        ["currency" => "USD", "hourly_rate" => 170.0],
                    ],
                ],
            ],
            "users"          => [
                [
                    "id"        => 933589840,
                    "full_name" => "Max Muster",
                    "rates"     => [
                        ["currency" => "CHF", "hourly_rate" => 160.0],
                        ["currency" => "EUR", "hourly_rate" => 150.0],
                        ["currency" => "USD", "hourly_rate" => 160.0],
                    ],
                ],
                [
                    "id"        => 933589844,
                    "full_name" => "Peter Müller",
                    "rates"     => [
                        ["currency" => "CHF", "hourly_rate" => 180.0],
                        ["currency" => "EUR", "hourly_rate" => 170.0],
                        ["currency" => "USD", "hourly_rate" => 180.0],
                    ],
                ],
            ],
        ];

        $this->mockResponse(200, json_encode($mockHourlyRates));
        $hourlyRates = $this->mocoClient->account->hourlyRates->get();

        $this->assertEquals('CHF', $hourlyRates->defaults_rates[0]->currency);
        $this->assertEquals(160.0, $hourlyRates->tasks[0]->rates[0]->hourly_rate);
        $this->assertEquals(170.0, $hourlyRates->users[1]->rates[1]->hourly_rate);
    }
}
