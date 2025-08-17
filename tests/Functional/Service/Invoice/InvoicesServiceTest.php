<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Invoice;

use Moco\Entity\Invoice;
use Tests\Functional\Service\AbstractServiceTest;

class InvoicesServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'customer_id' => 762610111,
        'recipient_address' => "Test Company\nTest Street 123\n12345 Test City",
        'date' => '2024-01-15',
        'due_date' => '2024-02-15',
        'title' => 'Test Invoice - Functional Test',
        'tax' => 19.0,
        'currency' => 'EUR',
        'items' => [
            [
                'type' => 'item',
                'title' => 'Consulting Services',
                'quantity' => 10.0,
                'unit' => 'h',
                'unit_price' => 100.0,
                'net_total' => 1000.0
            ]
        ]
    ];

    public function testCreate(): int
    {
        $invoice = $this->mocoClient->invoice->create($this->createParams);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('Test Invoice - Functional Test', $invoice->title);
        $this->assertEquals('EUR', $invoice->currency);
        return $invoice->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $invoiceId): int
    {
        $invoices = $this->mocoClient->invoice->get();
        $this->assertIsArray($invoices);

        $invoice = $this->mocoClient->invoice->get($invoiceId);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($invoiceId, $invoice->id);
        return $invoiceId;
    }

    /**
     * @depends testGet
     */
    public function testGetLocked(int $invoiceId): int
    {
        $lockedInvoices = $this->mocoClient->invoice->getLocked();
        $this->assertIsArray($lockedInvoices);
        return $invoiceId;
    }

    /**
     * @depends testGetLocked
     */
    public function testGetTimesheet(int $invoiceId): int
    {
        $timesheet = $this->mocoClient->invoice->getTimesheet($invoiceId);
        $this->assertIsArray($timesheet);
        return $invoiceId;
    }

    /**
     * @depends testGetTimesheet
     */
    public function testGetExpenses(int $invoiceId): int
    {
        $expenses = $this->mocoClient->invoice->getExpenses($invoiceId);
        $this->assertIsArray($expenses);
        return $invoiceId;
    }

    /**
     * @depends testGetExpenses
     */
    public function testUpdateStatus(int $invoiceId): int
    {
        $this->assertNull($this->mocoClient->invoice->updateStatus($invoiceId, 'created'));
        return $invoiceId;
    }

    /**
     * @depends testUpdateStatus
     */
    public function testGetAttachments(int $invoiceId): int
    {
        $attachments = $this->mocoClient->invoice->getAttachments($invoiceId);
        $this->assertIsArray($attachments);
        return $invoiceId;
    }

    /**
     * @depends testGetAttachments
     */
    public function testGetPdf(int $invoiceId): int
    {
        $pdfContent = $this->mocoClient->invoice->getPdf($invoiceId);
        $this->assertIsString($pdfContent);
        $this->assertStringStartsWith('%PDF', $pdfContent);
        return $invoiceId;
    }

    /**
     * @depends testGetPdf
     */
    public function testSendEmail(int $invoiceId): int
    {
        // Note: This test might actually send an email in a real environment
        // Consider skipping this in production or using test email addresses
        if (getenv('SKIP_EMAIL_TESTS') === 'true') {
            $this->markTestSkipped('Email tests skipped by environment variable');
        }

        $email = $this->mocoClient->invoice->sendEmail($invoiceId, [
            'recipients' => ['test@example.com'],
            'subject' => 'Test Invoice Email',
            'text' => 'Kind regards'
        ]);
        $this->assertEquals('Test Invoice Email', $email->subject);
        return $invoiceId;
    }

    /**
     * @depends testSendEmail
     */
    public function testDelete(int $invoiceId): void
    {
        $this->assertNull($this->mocoClient->invoice->delete($invoiceId, ['reason' => 'TEST DELETE']));
    }
}
