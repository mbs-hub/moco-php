<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Invoice;

use Moco\Entity\Invoice;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class InvoicesServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 123456,
        "identifier" => "R1704-001",
        "date" => "2017-04-07",
        "due_date" => "2017-05-07",
        "service_period_from" => "2017-03-01",
        "service_period_to" => "2017-03-31",
        "title" => "Rechnung März 2017",
        "recipient_address" => "Beispiel AG\nBeispielstrasse 123\n12345 Beispielstadt",
        "currency" => "EUR",
        "net_total" => 4000.0,
        "tax" => 19.0,
        "gross_total" => 4760.0,
        "discount" => "10%",
        "cash_discount" => ["days" => 5, "percentage" => 2],
        "status" => "paid",
        "sent_on" => "2017-04-07",
        "paid_on" => "2017-04-28",
        "payments" => [
            [
                "date" => "2017-04-28",
                "amount" => 4760.0,
                "method" => "bank_transfer",
                "note" => "Payment received"
            ]
        ],
        "reminders" => [],
        "locked" => true,
        "notes" => "Notes for invoice",
        "tags" => ["Web", "Design"],
        "custom_properties" => [
            "Project Management" => "https://example.com"
        ],
        "customer" => [
            "id" => 762610110,
            "name" => "Beispiel AG"
        ],
        "project" => [
            "id" => 947556942,
            "name" => "Website Relaunch"
        ],
        "items" => [
            [
                "id" => 1,
                "type" => "item",
                "title" => "Website Development",
                "description" => "Frontend and backend development",
                "quantity" => 20.0,
                "unit" => "h",
                "unit_price" => 200.0,
                "net_total" => 4000.0
            ]
        ],
        "created_at" => "2017-04-07T09:33:46Z",
        "updated_at" => "2017-04-07T09:33:46Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "customer_id" => 762610111,
            "recipient_address" => "Beispiel AG\nBeispielstrasse 123\n12345 Beispielstadt",
            "date" => "2017-04-07",
            "due_date" => "2017-05-07",
            "title" => "Rechnung März 2017",
            "tax" => 19.0,
            "currency" => "EUR"
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $invoice = $this->mocoClient->invoice->create($params);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals(123456, $invoice->id);

        unset($params['customer_id']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->invoice->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $invoices = $this->mocoClient->invoice->get();
        $this->assertIsArray($invoices);
        $this->assertEquals(123456, $invoices[0]->id);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $invoice = $this->mocoClient->invoice->get(123456);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals("R1704-001", $invoice->identifier);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->invoice->get(999999);
    }

    public function testGetLocked(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $lockedInvoices = $this->mocoClient->invoice->getLocked();
        $this->assertIsArray($lockedInvoices);
        $this->assertEquals(123456, $lockedInvoices[0]->id);
    }

    public function testGetPdf(): void
    {
        $pdfContent = "%PDF-1.4 sample pdf content";
        $this->mockResponse(200, $pdfContent);
        $result = $this->mocoClient->invoice->getPdf(123456);
        $this->assertEquals($pdfContent, $result);
    }

    public function testGetTimesheet(): void
    {
        $timesheetData = [
            [
                "id" => 789,
                "date" => "2017-03-01",
                "hours" => 8.0,
                "description" => "Development work"
            ]
        ];

        $this->mockResponse(200, json_encode($timesheetData));
        $timesheet = $this->mocoClient->invoice->getTimesheet(123456);
        $this->assertIsArray($timesheet);
        $this->assertEquals(789, $timesheet[0]->id);
    }

    public function testGetExpenses(): void
    {
        $expensesData = [
            [
                "id" => 456,
                "date" => "2017-03-15",
                "amount" => 100.0,
                "description" => "Travel expenses"
            ]
        ];

        $this->mockResponse(200, json_encode($expensesData));
        $expenses = $this->mocoClient->invoice->getExpenses(123456);
        $this->assertIsArray($expenses);
        $this->assertEquals(456, $expenses[0]->id);
    }

    public function testUpdateStatus(): void
    {
        $this->mockResponse(200);
        $result = $this->mocoClient->invoice->updateStatus(123456, 'sent');
        $this->assertNull($result);
    }

    public function testSendEmail(): void
    {
        $params = [
            'emails_to' => 'client@example.com',
            'subject' => 'Invoice R1704-001',
            'text' => 'TEST INVOICE'
        ];
        $this->mockResponse(200, json_encode($params));
        $invoice = $this->mocoClient->invoice->sendEmail(123456, $params);
        $this->assertEquals('client@example.com', $invoice->emails_to);
    }

    public function testGetAttachments(): void
    {
        $attachmentsData = [
            [
                "id" => 101,
                "filename" => "receipt.pdf",
                "size" => 1024
            ]
        ];

        $this->mockResponse(200, json_encode($attachmentsData));
        $attachments = $this->mocoClient->invoice->getAttachments(123456);
        $this->assertIsArray($attachments);
        $this->assertEquals(101, $attachments[0]->id);
    }

    public function testCreateAttachment(): void
    {
        $attachmentData = [
            "id" => 102,
            "filename" => "document.pdf",
            "size" => 2048
        ];

        $this->mockResponse(200, json_encode($attachmentData));
        $attachment = $this->mocoClient->invoice->createAttachment(123456, [
            'filename' => 'document.pdf',
            'file' => 'base64encodedcontent'
        ]);
        $this->assertEquals(102, $attachment->id);
    }

    public function testDeleteAttachment(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->invoice->deleteAttachment(123456, 102));
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->invoice->delete(123456, ['reason' => 'TEST DELETE']));
    }
}
