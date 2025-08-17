<?php

namespace Functional\Service\Purchases;

use Moco\Entity\PurchasePayment;
use Moco\Exception\InvalidRequestException;
use Tests\Functional\Service\AbstractServiceTest;

class PurchasePaymentsServiceTest extends AbstractServiceTest
{
    public function testCreate(): PurchasePayment
    {
        $params = [
            "date" => "2023-12-01",
            "total" => 150.50,
            "description" => "Test payment for functional testing"
        ];

        $payment = $this->mocoClient->purchasePayments->create($params);
        $this->assertInstanceOf(PurchasePayment::class, $payment);
        $this->assertEquals($params['date'], $payment->date);
        $this->assertEquals($params['total'], $payment->total);

        return $payment;
    }

    /**
     * @depends testCreate
     */
    public function testGet(PurchasePayment $payment): int
    {
        $payments = $this->mocoClient->purchasePayments->get();
        $this->assertIsArray($payments);

        $result = $this->mocoClient->purchasePayments->get(['total' => 150.50]);
        $this->assertIsArray($result);

        $singlePayment = $this->mocoClient->purchasePayments->get($payment->id);
        $this->assertEquals($payment->id, $singlePayment->id);

        return $payment->id;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $paymentId): int
    {
        $payment = $this->mocoClient->purchasePayments->update($paymentId, ['total' => 200.75]);
        $this->assertInstanceOf(PurchasePayment::class, $payment);
        $this->assertEquals(200.75, $payment->total);

        return $paymentId;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $paymentId): void
    {
        $this->assertNull($this->mocoClient->purchasePayments->delete($paymentId));
    }

    public function testCreateBulk(): void
    {
        $bulkPayments = [
            [
                "date" => "2023-12-01",
                "total" => 100.0,
                "description" => "Bulk payment 1"
            ],
            [
                "date" => "2023-12-02",
                "total" => 200.0,
                "description" => "Bulk payment 2"
            ]
        ];

        $payments = $this->mocoClient->purchasePayments->createBulk($bulkPayments);
        $this->assertIsArray($payments);
        $this->assertCount(2, $payments);

        foreach ($payments as $payment) {
            $this->assertInstanceOf(PurchasePayment::class, $payment);
            $this->assertIsInt($payment->id);
        }

        // Clean up created payments
        foreach ($payments as $payment) {
            try {
                $this->mocoClient->purchasePayments->delete($payment->id);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }
    }

    public function testGetWithFilters(): void
    {
        $payments = $this->mocoClient->purchasePayments->get([
            'date_from' => '2023-01-01',
            'date_to' => '2023-12-31'
        ]);
        $this->assertIsArray($payments);

        foreach ($payments as $payment) {
            $this->assertInstanceOf(PurchasePayment::class, $payment);
        }
    }
}
