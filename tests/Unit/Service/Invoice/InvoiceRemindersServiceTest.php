<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Invoice;

use Moco\Entity\InvoiceReminder;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class InvoiceRemindersServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 12345,
        "title" => "Zahlungserinnerung",
        "text" => "Dear customer, this is a reminder that your payment is overdue. Please process the payment as soon as possible.",
        "fee" => 5.00,
        "date" => "2019-10-16",
        "due_date" => "2019-10-30",
        "invoice" => [
            "id" => 1489,
            "identifier" => "1409-019",
            "title" => "Rechnung - Android Prototype"
        ],
        "created_at" => "2019-10-16T10:15:30Z",
        "updated_at" => "2019-10-16T10:15:30Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "invoice_id" => 1489,
            "title" => "Zahlungserinnerung",
            "text" => "Payment reminder text",
            "fee" => 5.00,
            "date" => "2019-10-16",
            "due_date" => "2019-10-30"
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $reminder = $this->mocoClient->invoiceReminders->create($params);
        $this->assertInstanceOf(InvoiceReminder::class, $reminder);
        $this->assertEquals(12345, $reminder->id);
        $this->assertEquals("Zahlungserinnerung", $reminder->title);

        unset($params['invoice_id']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->invoiceReminders->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $reminders = $this->mocoClient->invoiceReminders->get();
        $this->assertIsArray($reminders);
        $this->assertEquals(12345, $reminders[0]->id);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $reminder = $this->mocoClient->invoiceReminders->get(12345);
        $this->assertInstanceOf(InvoiceReminder::class, $reminder);
        $this->assertEquals("2019-10-16", $reminder->date);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->invoiceReminders->get(999999);
    }

    public function testSendEmail(): void
    {
        $emailParams = [
            "subject" => "Payment Reminder for Invoice 1409-019",
            "text" => "Dear customer, please pay your overdue invoice.",
            "emails_to" => "client@example.com",
            "emails_cc" => "manager@example.com"
        ];

        $this->mockResponse(200, json_encode($emailParams));
        $email = $this->mocoClient->invoiceReminders->sendEmail(12345, $emailParams);
        $this->assertEquals('Payment Reminder for Invoice 1409-019', $email->subject);

        // Test missing mandatory fields
        unset($emailParams['subject']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->invoiceReminders->sendEmail(12345, $emailParams);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->invoiceReminders->delete(12345));
    }
}
