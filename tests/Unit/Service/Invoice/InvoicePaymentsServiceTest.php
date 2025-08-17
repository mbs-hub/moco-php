<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Invoice;

use Moco\Entity\InvoicePayment;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class InvoicePaymentsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 12345,
        "date" => "2017-10-01",
        "invoice" => [
            "id" => 67890,
            "identifier" => "R1710-001",
            "title" => "Invoice – Website"
        ],
        "paid_total" => 17999.00,
        "currency" => "EUR",
        "partially_paid" => false,
        "description" => "Payment via bank transfer",
        "created_at" => "2017-10-01T10:15:30Z",
        "updated_at" => "2017-10-01T10:15:30Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "date" => "2017-10-01",
            "paid_total" => 17999.00,
            "invoice_id" => 67890,
            "currency" => "EUR",
            "description" => "Payment via bank transfer"
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $payment = $this->mocoClient->invoicePayments->create($params);
        $this->assertInstanceOf(InvoicePayment::class, $payment);
        $this->assertEquals(12345, $payment->id);
        $this->assertEquals(17999.00, $payment->paid_total);

        unset($params['date']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->invoicePayments->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $payments = $this->mocoClient->invoicePayments->get();
        $this->assertIsArray($payments);
        $this->assertEquals(12345, $payments[0]->id);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $payment = $this->mocoClient->invoicePayments->get(12345);
        $this->assertInstanceOf(InvoicePayment::class, $payment);
        $this->assertEquals("2017-10-01", $payment->date);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->invoicePayments->get(999999);
    }

    public function testUpdate(): void
    {
        $updatedResponse = $this->expectedResponse;
        $updatedResponse['description'] = 'Updated payment description';

        $this->mockResponse(200, json_encode($updatedResponse));
        $payment = $this->mocoClient->invoicePayments->update(12345, [
            'description' => 'Updated payment description'
        ]);
        $this->assertInstanceOf(InvoicePayment::class, $payment);
        $this->assertEquals('Updated payment description', $payment->description);
    }

    public function testCreateBulk(): void
    {
        $payments = [
            [
                "date" => "2017-10-01",
                "paid_total" => 5000.00,
                "invoice_id" => 67890
            ],
            [
                "date" => "2017-10-02",
                "paid_total" => 3000.00,
                "invoice_id" => 67891
            ]
        ];

        $bulkResponse = [
            array_merge($this->expectedResponse, ["id" => 12345, "paid_total" => 5000.00]),
            array_merge($this->expectedResponse, ["id" => 12346, "paid_total" => 3000.00])
        ];

        $this->mockResponse(200, json_encode($bulkResponse));
        $createdPayments = $this->mocoClient->invoicePayments->createBulk($payments);
        $this->assertIsArray($createdPayments);
        $this->assertCount(2, $createdPayments);
        $this->assertEquals(12345, $createdPayments[0]->id);
        $this->assertEquals(12346, $createdPayments[1]->id);

        $invalidPayments = [
            [
                "paid_total" => 5000.00  // Missing required 'date' field
            ]
        ];
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->invoicePayments->createBulk($invalidPayments);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->invoicePayments->delete(12345));
    }
}
