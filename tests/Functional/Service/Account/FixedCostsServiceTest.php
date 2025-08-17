<?php

namespace Tests\Functional\Service\Account;

use Tests\Functional\Service\AbstractServiceTest;

class FixedCostsServiceTest extends AbstractServiceTest
{
    public function testGet(): void
    {
        $costs = $this->mocoClient->account->fixedCosts->get();
        $this->assertIsArray($costs);
    }
}
