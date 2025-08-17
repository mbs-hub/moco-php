<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Invoice;

use Moco\Entity\InvoiceBookkeepingExport;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class InvoiceBookkeepingExportsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 12345,
        "from" => "2024-01-01",
        "to" => "2024-01-31",
        "invoice_ids" => [123, 234, 345],
        "comment" => "Monthly export for January 2024",
        "user" => [
            "id" => 456,
            "firstname" => "John",
            "lastname" => "Doe"
        ],
        "status" => "manual",
        "created_at" => "2024-01-31T10:15:30Z",
        "updated_at" => "2024-01-31T10:15:30Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "invoice_ids" => [123, 234, 345],
            "comment" => "Monthly export for January 2024",
            "trigger_submission" => true
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $export = $this->mocoClient->invoiceBookkeepingExports->create($params);
        $this->assertInstanceOf(InvoiceBookkeepingExport::class, $export);
        $this->assertEquals(12345, $export->id);
        $this->assertEquals([123, 234, 345], $export->invoice_ids);

        unset($params['invoice_ids']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->invoiceBookkeepingExports->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $exports = $this->mocoClient->invoiceBookkeepingExports->get();
        $this->assertIsArray($exports);
        $this->assertEquals(12345, $exports[0]->id);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $export = $this->mocoClient->invoiceBookkeepingExports->get(12345);
        $this->assertInstanceOf(InvoiceBookkeepingExport::class, $export);
        $this->assertEquals("Monthly export for January 2024", $export->comment);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->invoiceBookkeepingExports->get(999999);
    }
}
