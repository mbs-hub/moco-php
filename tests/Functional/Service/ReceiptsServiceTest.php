<?php

namespace Functional\Service;

use Moco\Entity\Receipt;
use Moco\Exception\InvalidRequestException;
use Tests\Functional\Service\AbstractServiceTest;

class ReceiptsServiceTest extends AbstractServiceTest
{
    public function testCreate(): Receipt
    {
        $params = [
            "title" => "Test Receipt",
            "date" => "2023-12-01",
            "currency" => "EUR",
            "billable" => true,
            "items" => [
                [
                    "description" => "Test item",
                    "gross_total" => 100.50,
                    "vat_code_id" => 119458,
                ]
            ],
            'project_id' => 947556942
        ];

        $receipt = $this->mocoClient->receipts->create($params);
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertEquals($params['title'], $receipt->title);
        $this->assertEquals($params['date'], $receipt->date);
        $this->assertEquals($params['currency'], $receipt->currency);

        return $receipt;
    }

    /**
     * @depends testCreate
     */
    public function testGet(Receipt $receipt): int
    {
        $receipts = $this->mocoClient->receipts->get();
        $this->assertIsArray($receipts);

        $filteredReceipts = $this->mocoClient->receipts->get(['currency' => 'EUR']);
        $this->assertIsArray($filteredReceipts);

        $singleReceipt = $this->mocoClient->receipts->get($receipt->id);
        $this->assertEquals($receipt->id, $singleReceipt->id);

        return $receipt->id;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $receiptId): int
    {
        $receipt = $this->mocoClient->receipts->update($receiptId, ['title' => 'Updated Test Receipt']);
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertEquals('Updated Test Receipt', $receipt->title);

        return $receiptId;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $receiptId): void
    {
        $this->assertNull($this->mocoClient->receipts->delete($receiptId));
    }

    public function testGetWithFilters(): void
    {
        $receipts = $this->mocoClient->receipts->get([
            'date_from' => '2023-01-01',
            'date_to' => '2023-12-31'
        ]);
        $this->assertIsArray($receipts);

        foreach ($receipts as $receipt) {
            $this->assertInstanceOf(Receipt::class, $receipt);
        }

        // Test project filter
        $projectReceipts = $this->mocoClient->receipts->get(['project_id' => 947556942]);
        $this->assertIsArray($projectReceipts);

        // Test user filter
        $userReceipts = $this->mocoClient->receipts->get(['user_id' => 933736920]);
        $this->assertIsArray($userReceipts);
    }

    public function testCreateWithAttachment(): void
    {
        // Create a simple base64 encoded test file
        $testContent = "Test receipt content";
        $base64Content = base64_encode($testContent);

        $params = [
            "title" => "Receipt with attachment",
            "date" => "2023-12-01",
            "currency" => "EUR",
            "items" => [
                [
                    "description" => "Equipment with receipt",
                    "gross_total" => 200.00,
                    "vat_code" => "19%",
                    "purchase_category_id" => 1
                ]
            ],
            "attachment" => [
                "filename" => "test_receipt.txt",
                "base64" => $base64Content
            ]
        ];

        $receipt = $this->mocoClient->receipts->create($params);
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertEquals('test_receipt.txt', $receipt->attachment_filename);

        // Clean up
        try {
            $this->mocoClient->receipts->delete($receipt->id);
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }

    public function testValidationErrors(): void
    {
        // Test missing required fields
        $invalidParams = [
            "title" => "Invalid receipt"
            // Missing date, currency, and items
        ];

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->receipts->create($invalidParams);
    }
}
