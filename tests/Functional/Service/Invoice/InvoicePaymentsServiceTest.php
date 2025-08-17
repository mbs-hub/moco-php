<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Invoice;

use Moco\Entity\InvoicePayment;
use Tests\Functional\Service\AbstractServiceTest;

class InvoicePaymentsServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'date' => '2024-01-15',
        'paid_total' => 1500.00,
        'invoice_id' => 6002049, // This should be replaced with a valid invoice ID in actual testing
        'currency' => 'EUR',
        'description' => 'Test payment - Functional Test',
        'partially_paid' => false
    ];

    public function testCreate(): int
    {
        $payment = $this->mocoClient->invoicePayments->create($this->createParams);
        $this->assertInstanceOf(InvoicePayment::class, $payment);
        $this->assertEquals('Test payment - Functional Test', $payment->description);
        $this->assertEquals(1500.00, $payment->paid_total);
        return $payment->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $paymentId): int
    {
        $payments = $this->mocoClient->invoicePayments->get();
        $this->assertIsArray($payments);

        $payment = $this->mocoClient->invoicePayments->get($paymentId);
        $this->assertInstanceOf(InvoicePayment::class, $payment);
        $this->assertEquals($paymentId, $payment->id);
        return $paymentId;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $paymentId): int
    {
        $payment = $this->mocoClient->invoicePayments->update($paymentId, [
            'description' => 'Updated test payment description'
        ]);
        $this->assertInstanceOf(InvoicePayment::class, $payment);
        $this->assertEquals('Updated test payment description', $payment->description);
        return $paymentId;
    }

    /**
     * @depends testUpdate
     */
    public function testCreateBulk(int $existingPaymentId): int
    {
        $bulkPayments = [
            [
                'date'        => '2024-01-16',
                'paid_total'  => 500.00,
                'invoice_id'  => 6002048,
                'currency'    => 'EUR',
                'description' => 'Bulk payment 1',
            ],
            [
                'date'        => '2024-01-17',
                'paid_total'  => 750.00,
                'invoice_id'  => 6002041,
                'currency'    => 'EUR',
                'description' => 'Bulk payment 2',
            ],
        ];

        $createdPayments = $this->mocoClient->invoicePayments->createBulk($bulkPayments);
        $this->assertIsArray($createdPayments);
        $this->assertCount(2, $createdPayments);

        foreach ($createdPayments as $payment) {
            $this->assertInstanceOf(InvoicePayment::class, $payment);
        }

        // Clean up bulk payments
        foreach ($createdPayments as $payment) {
            $this->mocoClient->invoicePayments->delete($payment->id);
        }

        return $existingPaymentId;
    }

    /**
     * @depends testCreateBulk
     */
    public function testGetWithFilters(int $paymentId): int
    {
        // Test getting payments with date filter
        $filteredPayments = $this->mocoClient->invoicePayments->get([
            'date_from' => '2024-01-01',
            'date_to' => '2024-12-31'
        ]);
        $this->assertIsArray($filteredPayments);

        // Verify our created payment is in the filtered results
        $found = false;
        foreach ($filteredPayments as $payment) {
            if ($payment->id === $paymentId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created payment should be found in filtered results');

        return $paymentId;
    }

    /**
     * @depends testGetWithFilters
     */
    public function testDelete(int $paymentId): void
    {
        $this->assertNull($this->mocoClient->invoicePayments->delete($paymentId));
    }
}
