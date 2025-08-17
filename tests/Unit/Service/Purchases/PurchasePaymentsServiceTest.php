<?php

namespace Tests\Unit\Service\Purchases;

use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class PurchasePaymentsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 123,
        "date" => "2018-10-20",
        "purchase" => [
            "id" => 456,
            "identifier" => "P001",
            "title" => "Office supplies"
        ],
        "total" => 1000.0,
        "created_at" => "2018-10-20T10:00:00Z",
        "updated_at" => "2018-10-20T10:00:00Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "date" => "2018-10-20",
            "purchase_id" => 456,
            "total" => 1000.0
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $payment = $this->mocoClient->purchasePayments->create($params);
        $this->assertEquals(123, $payment->id);
        $this->assertEquals(1000.0, $payment->total);

        unset($params['date']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->purchasePayments->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $payments = $this->mocoClient->purchasePayments->get();
        $this->assertEquals(123, $payments[0]->id);
        $this->assertEquals("2018-10-20", $payments[0]->date);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $payment = $this->mocoClient->purchasePayments->get(123);
        $this->assertEquals(1000.0, $payment->total);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->purchasePayments->get(999);
    }

    public function testGetWithParams(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $payments = $this->mocoClient->purchasePayments->get(['purchase_id' => 456]);
        $this->assertEquals(123, $payments[0]->id);
    }

    public function testUpdate(): void
    {
        $this->expectedResponse['total'] = 1200.0;
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $payment = $this->mocoClient->purchasePayments->update(123, ['total' => 1200.0]);
        $this->assertEquals(1200.0, $payment->total);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->purchasePayments->delete(123));
    }

    public function testCreateBulk(): void
    {
        $bulkPayments = [
            [
                "date" => "2018-10-20",
                "purchase_id" => 456,
                "total" => 1000.0
            ],
            [
                "date" => "2018-10-21",
                "purchase_id" => 457,
                "total" => 2000.0
            ]
        ];

        $bulkResponse = [
            $this->expectedResponse,
            [
                "id" => 124,
                "date" => "2018-10-21",
                "purchase" => [
                    "id" => 457,
                    "identifier" => "P002",
                    "title" => "Equipment"
                ],
                "total" => 2000.0,
                "created_at" => "2018-10-21T10:00:00Z",
                "updated_at" => "2018-10-21T10:00:00Z"
            ]
        ];

        $this->mockResponse(200, json_encode($bulkResponse));
        $payments = $this->mocoClient->purchasePayments->createBulk($bulkPayments);
        $this->assertCount(2, $payments);
        $this->assertEquals(123, $payments[0]->id);
        $this->assertEquals(124, $payments[1]->id);

        // Test validation error
        $invalidBulkPayments = [
            [
                "purchase_id" => 456,
                "total" => 1000.0
                // Missing required 'date' field
            ]
        ];

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->purchasePayments->createBulk($invalidBulkPayments);
    }
}
