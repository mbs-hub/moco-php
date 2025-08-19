<?php

namespace Tests\Functional\Service\Account;

use Tests\Functional\Service\FunctionalTestCase;

class CustomPropertiesTest extends FunctionalTestCase
{
    public function testGet(): void
    {
        $customProperties = $this->mocoClient->account->customProperties->get();
        $this->assertIsArray($customProperties);
    }
}
