<?php

namespace Functional\Service\Purchases;

use Moco\Entity\Purchase;
use Moco\Exception\InvalidRequestException;
use Tests\Functional\Service\AbstractServiceTest;
use Tests\Functional\Service\CompaniesServiceTest;

class PurchasesServiceTest extends AbstractServiceTest
{
    public function testCreate(): Purchase
    {
        $params = [
            "date"           => "2023-12-01",
            "currency"       => "EUR",
            "payment_method" => "bank_transfer",
            "company_id"     => 762611472,
            "items"          => [
                [
                    "title"        => "Ticket",
                    "total"        => 30,
                    "tax"          => 19,
                    "tax_included" => true,
                ],
            ],
            "tags"           => ["Office", "Equipment"],
        ];

        $purchase = $this->mocoClient->purchases->create($params);
        $this->assertInstanceOf(Purchase::class, $purchase);
        $this->assertEquals($params['currency'], $purchase->currency);
        $this->assertEquals($params['payment_method'], $purchase->payment_method);

        return $purchase;
    }

    /**
     * @depends testCreate
     */
    public function testGet(Purchase $purchase): int
    {
        $purchases = $this->mocoClient->purchases->get();
        $this->assertIsArray($purchases);

        $result = $this->mocoClient->purchases->get(['status' => 'pending']);
        $this->assertIsArray($result);

        $singlePurchase = $this->mocoClient->purchases->get($purchase->id);
        $this->assertEquals($purchase->id, $singlePurchase->id);

        return $purchase->id;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $purchaseId): int
    {
        $purchase = $this->mocoClient->purchases->update($purchaseId, ['info' => 'Updated info']);
        $this->assertInstanceOf(Purchase::class, $purchase);
        $this->assertEquals('Updated info', $purchase->info);

        return $purchaseId;
    }

    /**
     * @depends testUpdate
     */
    public function testUpdateStatus(int $purchaseId): int
    {
        $this->assertNull($this->mocoClient->purchases->updateStatus($purchaseId, 'archived'));
        return $purchaseId;
    }

    /**
     * @depends testUpdateStatus
     */
    public function testDelete(int $purchaseId): void
    {
        $this->expectException(InvalidRequestException::class);
        ;
        $this->mocoClient->purchases->delete($purchaseId);
    }
}
