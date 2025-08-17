<?php

namespace Tests\Functional\Service\Account;

use Tests\Functional\Service\AbstractServiceTest;

class InternalHourlyRatesServiceTest extends AbstractServiceTest
{
    public function testGet(): array
    {
        $result = $this->mocoClient->account->internalHourlyRates->get();
        $this->assertIsArray($result);
        return $result;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(array $rates): void
    {
        if (isset($rates[0])) {
            $userId = $rates[0]->id;
            $updateParams = [
                'year' => 2022,
                'rates' => [
                    [
                        'user_id' => $userId,
                        'rate' => 140.0
                    ]
                ]
            ];
            $result = $this->mocoClient->account->internalHourlyRates->update($updateParams);
            $this->assertTrue($result);
        }
    }
}
