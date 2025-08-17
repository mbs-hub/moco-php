<?php

namespace Tests\Unit\Service\Account;

use Tests\Unit\Service\AbstractServiceTest;

class FixedCostsServiceTest extends AbstractServiceTest
{
    public function testGet(): void
    {
        $mockedFixedCosts = [
            "id"          => 123,
            "title"       => "Salaries",
            "description" => "Monhtly total salaries for the company",
            "costs"       => [
                [
                    "year"   => 2020,
                    "month"  => 1,
                    "amount" => 100000.0,
                ],
                [
                    "year"   => 2020,
                    "month"  => 2,
                    "amount" => 100000.0,
                ],
                [
                    "year"   => 2020,
                    "month"  => 3,
                    "amount" => 100000.0,
                ],
            ],
            "created_at"  => "2018-10-17T09:33:46Z",
            "updated_at"  => "2018-10-17T09:33:46Z",
        ];

        $this->mockResponse(200, json_encode([$mockedFixedCosts]));
        $costs = $this->mocoClient->account->fixedCosts->get();
        $this->assertEquals(123, $costs[0]->id);
    }
}
