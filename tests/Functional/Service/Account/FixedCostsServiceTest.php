<?php

namespace Tests\Functional\Service\Account;

use Tests\Functional\Service\FunctionalTestCase;

class FixedCostsServiceTest extends FunctionalTestCase
{
    public function testGet(): void
    {
        $costs = $this->mocoClient->account->fixedCosts->get();
        $this->assertIsArray($costs);
    }
}
