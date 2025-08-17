<?php

namespace Tests\Unit\Service\Purchases;

use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class PurchasesServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 987,
        "date" => "2020-02-28",
        "net_total" => 44.88,
        "gross_total" => 46.0,
        "currency" => "CHF",
        "status" => "pending",
        "payment_method" => "bank_transfer",
        "info" => "Purchase description",
        "tags" => ["Office", "Equipment"],
        "items" => [
            [
                "id" => 1,
                "title" => "Office supplies",
                "quantity" => 1,
                "unit" => "piece",
                "unit_price" => 44.88,
                "net_total" => 44.88,
                "vat_percent" => 2.5
            ]
        ],
        "company" => [
            "id" => 123,
            "name" => "Supplier AG"
        ],
        "user" => [
            "id" => 456,
            "firstname" => "John",
            "lastname" => "Doe"
        ],
        "payments" => [],
        "approval_required" => false,
        "approval_status" => "approved",
        "custom_properties" => [],
        "created_at" => "2020-02-28T10:00:00Z",
        "updated_at" => "2020-02-28T10:00:00Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "date" => "2020-02-28",
            "currency" => "CHF",
            "payment_method" => "bank_transfer",
            "items" => [
                [
                    "title" => "Office supplies",
                    "quantity" => 1,
                    "unit" => "piece",
                    "unit_price" => 44.88,
                    "vat_percent" => 2.5
                ]
            ]
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $purchase = $this->mocoClient->purchases->create($params);
        $this->assertEquals(987, $purchase->id);

        unset($params['date']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->purchases->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $purchases = $this->mocoClient->purchases->get();
        $this->assertEquals(987, $purchases[0]->id);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $purchase = $this->mocoClient->purchases->get(987);
        $this->assertEquals("pending", $purchase->status);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->purchases->get(12);
    }

    public function testUpdate(): void
    {
        $this->expectedResponse['info'] = 'Updated description';
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $purchase = $this->mocoClient->purchases->update(987, ['info' => 'Updated description']);
        $this->assertEquals('Updated description', $purchase->info);
    }

    public function testAssignToProject(): void
    {
        $params = [
            "project_id" => 123,
            "item_id" => 1
        ];
        $expectedResponse = ["success" => true];

        $this->mockResponse(200, json_encode($expectedResponse));
        $result = $this->mocoClient->purchases->assignToProject(987, $params);
        $this->assertTrue($result->success);
    }

    public function testUpdateStatus(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->purchases->updateStatus(987, 'archived'));
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->purchases->delete(987));
    }
}
