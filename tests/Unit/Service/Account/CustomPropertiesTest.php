<?php

namespace Tests\Unit\Service\Account;

use Moco\Exception\InvalidRequestException;
use Tests\Unit\Service\AbstractServiceTest;

class CustomPropertiesTest extends AbstractServiceTest
{
    public function testGet(): void
    {
        $mockResult = [
            "id"                   => 8601,
            "name"                 => "Purchase Ordner Number",
            "name_en"              => "Purchase Ordner Number",
            "placeholder"          => "",
            "placeholder_en"       => "",
            "entity"               => "Project",
            "kind"                 => "String",
            "print_on_invoice"     => true,
            "print_on_offer"       => false,
            "print_on_timesheet"   => true,
            "notification_enabled" => false,
            "defaults"             => [],
            "updated_at"           => "2022-08-15T15:31:28Z",
            "created_at"           => "2022-08-15T15:31:22Z",
        ];

        $this->mockResponse(200, json_encode([$mockResult]));
        $customProperties = $this->mocoClient->account->customProperties->get();
        $this->assertIsArray($customProperties);
        $this->assertEquals(8601, $customProperties[0]->id);

        $this->mockResponse(200, json_encode($mockResult));
        $customProperties = $this->mocoClient->account->customProperties->get(8601);
        $this->assertEquals(8601, $customProperties->id);
    }
}
