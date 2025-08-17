<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Invoice;

use Amp\Emitter;
use Moco\Entity\InvoiceReminder;
use Moco\Struct\Email;
use Tests\Functional\Service\AbstractServiceTest;

class InvoiceRemindersServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'invoice_id' => 6002041, // This should be replaced with a valid invoice ID in actual testing
        'title' => 'Test Payment Reminder',
        'text' => 'This is a test payment reminder - Functional Test',
        'fee' => 10.00,
        'date' => '2024-01-20',
        'due_date' => '2024-02-05'
    ];

    public function testCreate(): int
    {
        $reminder = $this->mocoClient->invoiceReminders->create($this->createParams);
        $this->assertInstanceOf(InvoiceReminder::class, $reminder);
        $this->assertEquals('Test Payment Reminder', $reminder->title);
        $this->assertEquals(10.00, $reminder->fee);
        return $reminder->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $reminderId): int
    {
        $reminders = $this->mocoClient->invoiceReminders->get();
        $this->assertIsArray($reminders);

        $reminder = $this->mocoClient->invoiceReminders->get($reminderId);
        $this->assertInstanceOf(InvoiceReminder::class, $reminder);
        $this->assertEquals($reminderId, $reminder->id);
        return $reminderId;
    }

    /**
     * @depends testGet
     */
    public function testGetWithFilters(int $reminderId): int
    {
        // Test getting reminders with date filter
        $filteredReminders = $this->mocoClient->invoiceReminders->get([
            'date_from' => '2024-01-01',
            'date_to' => '2024-12-31'
        ]);
        $this->assertIsArray($filteredReminders);

        // Verify our created reminder is in the filtered results
        $found = false;
        foreach ($filteredReminders as $reminder) {
            if ($reminder->id === $reminderId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created reminder should be found in filtered results');

        return $reminderId;
    }

    /**
     * @depends testGetWithFilters
     */
    public function testSendEmail(int $reminderId): int
    {
        // Note: This test might actually send an email in a real environment
        // Consider skipping this in production or using test email addresses
        if (getenv('SKIP_EMAIL_TESTS') === 'true') {
            $this->markTestSkipped('Email tests skipped by environment variable');
        }

        $emailParams = [
            'subject' => 'Test Payment Reminder Email',
            'text' => 'This is a test payment reminder email from functional tests.',
            'emails_to' => ['test@example.com']
        ];

        $email = $this->mocoClient->invoiceReminders->sendEmail($reminderId, $emailParams);
        $this->assertEquals($emailParams['text'], $email->text);

        return $reminderId;
    }

    /**
     * @depends testSendEmail
     */
    public function testGetByInvoiceId(int $reminderId): int
    {
        // Test getting reminders filtered by invoice_id
        $invoiceReminders = $this->mocoClient->invoiceReminders->get([
            'invoice_id' => 6002041
        ]);
        $this->assertIsArray($invoiceReminders);

        // Verify our created reminder is in the invoice-specific results
        $found = false;
        foreach ($invoiceReminders as $reminder) {
            if ($reminder->id === $reminderId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created reminder should be found when filtering by invoice_id');

        return $reminderId;
    }

    /**
     * @depends testGetByInvoiceId
     */
    public function testDelete(int $reminderId): void
    {
        $this->assertNull($this->mocoClient->invoiceReminders->delete($reminderId));
    }
}
